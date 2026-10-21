<?php

namespace Modules\Connector\Http\Controllers\Api;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Connector\Support\DesktopPasswordLogin;
use Modules\Connector\Support\DesktopTokenIssuer;
use Nyholm\Psr7\ServerRequest;

class DesktopLoginController extends Controller
{
    private $login;
    private $app;
    private $config;

    public function __construct(DesktopPasswordLogin $login, Container $app, Repository $config)
    {
        $this->login = $login;
        $this->app = $app;
        $this->config = $config;
    }

    public function login(Request $request)
    {
        try {
            // Production always requires TLS. Local HTTP is ONLY for literal
            // loopback hosts, never arbitrary hosts merely because APP_ENV=local.
            if (! $request->isSecure() && ! ($this->config->get('app.env') === 'local'
                && in_array($request->getHost(), ['localhost', '127.0.0.1', '[::1]'], true))) {
                return $this->failure();
            }

            // Accept credentials from the POST body only. Query parameters and
            // Basic authorization cannot override the submitted credentials.
            $input = $request->isJson() ? $request->json()->all() : $request->request->all();
            if (($input['source'] ?? null) !== 'desktop_app'
                || ! in_array($input['access'] ?? null, ['admin', 'cashier'], true)) {
                return $this->failure();
            }
            foreach (['client_id' => 255, 'client_secret' => 4096, 'password' => 4096] as $field => $maximum) {
                if (! isset($input[$field]) || ! is_string($input[$field])
                    || $input[$field] === '' || strlen($input[$field]) > $maximum
                    || strpos($input[$field], "\0") !== false) {
                    return $this->failure();
                }
            }
            if ($input['access'] === 'cashier'
                && (strlen($input['password']) > 6 || ! ctype_digit($input['password']))) {
                return $this->failure();
            }
            $username = null;
            if ($input['access'] === 'admin') {
                if (! isset($input['username']) || ! is_string($input['username'])
                    || $input['username'] === '' || strlen($input['username']) > 255
                    || strpos($input['username'], "\0") !== false) {
                    return $this->failure();
                }
                $username = $input['username'];
            }

            $user = $this->login->resolve(
                $input['client_id'],
                $input['client_secret'],
                $input['password'],
                $input['access'],
                $username
            );
            if (! $user) {
                return $this->failure();
            }

            // A fresh PSR-7 request prevents incoming Basic auth/query/cookies from
            // taking precedence in League's password grant. Passport issues its
            // normal access/refresh tokens; no synthetic identities or token writes.
            $oauthRequest = (new ServerRequest('POST', 'https://localhost/oauth/token', [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ]))->withParsedBody([
                'grant_type' => 'password',
                'client_id' => $input['client_id'],
                'client_secret' => $input['client_secret'],
                'username' => $user->username,
                'password' => $input['password'],
                'scope' => '',
            ]);
            // Resolve lazily: invalid/unauthorized requests never load signing keys
            // or construct/call the authorization server.
            $response = $this->app->make(DesktopTokenIssuer::class)->issueToken($oauthRequest, $user, $input['access']);
            if ($response->getStatusCode() !== 200) {
                return $this->failure();
            }
            $response->headers->set('Cache-Control', 'no-store');
            $response->headers->set('Pragma', 'no-cache');

            return $response;
        } catch (\Throwable $ignored) {
            // Do not report/log exceptions containing credentials or token payloads.
            return $this->failure();
        }
    }

    private function failure(): JsonResponse
    {
        return new JsonResponse(['error' => 'invalid_grant', 'message' => 'Unable to authenticate.'], 401, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache',
        ]);
    }
}