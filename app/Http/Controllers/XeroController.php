<?php

namespace App\Http\Controllers;

use ExampleClass;
use Illuminate\Http\Request;
use Webfox\Xero\OauthCredentialManager;

class XeroController extends Controller
{
    protected $xeroTenantId = null;
    protected $apiInstance = null;


    public function index(Request $request, OauthCredentialManager $xeroCredentials)
    {
        try {
            // Check if we've got any stored credentials
            if ($xeroCredentials->exists()) {
                /*
                 * We have stored credentials so we can resolve the AccountingApi,
                 * If we were sure we already had some stored credentials then we could just resolve this through the controller
                 * But since we use this route for the initial authentication we cannot be sure!
                 */
                $xero             = resolve(\XeroAPI\XeroPHP\Api\AccountingApi::class);
                $organisationName = $xero->getOrganisations($xeroCredentials->getTenantId())->getOrganisations()[0]->getName();
                $user             = $xeroCredentials->getUser();
                $username         = "{$user['given_name']} {$user['family_name']} ({$user['username']})";
                $this->xeroTenantId = $xeroCredentials->getTenantId();
                $this->apiInstance = $xero;
            }
        } catch (\throwable $e) {
            // This can happen if the credentials have been revoked or there is an error with the organisation (e.g. it's expired)
            $error = $e->getMessage();
        }

        return view('xero', [
            'connected'        => $xeroCredentials->exists(),
            'error'            => $error ?? null,
            'organisationName' => $organisationName ?? null,
            'username'         => $username ?? null
        ]);
    }

    public function getContact($xeroTenantId, $apiInstance, $returnObj = false)
    {
        $example = new ExampleClass();
        $contact = $example->getContact($this->xeroTenantId, $this->apiInstance, true);

        return $contact;
    }
}
