<?php

namespace App\Containers\AppSection\Authentication\UI\API\Controllers\WebClient;

use Apiato\Support\Facades\Response;
use App\Containers\AppSection\Authentication\Actions\Api\WebClient\IssueTokenAction;
use App\Containers\AppSection\Authentication\UI\API\Requests\WebClient\IssueTokenRequest;
use App\Containers\AppSection\Authentication\UI\API\Transformers\PasswordTokenTransformer;
use App\Containers\AppSection\Authentication\Values\UserCredential;
use App\Containers\AppSection\User\Models\User;
use App\Ship\Parents\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

final class IssueTokenController extends ApiController
{
    public function __invoke(IssueTokenRequest $request, IssueTokenAction $action): JsonResponse
    {
        $result = $action->run(
            UserCredential::createFrom($request),
        );

        $user = User::with('roles')->where('email', $request->email)->first();

        return response()->json([
            'data' => [
                'type' => $result->getResourceKey(),
                'token_type' => $result->tokenType,
                'access_token' => $result->accessToken,
                'expires_in' => $result->expiresIn,
            ],
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('code')->toArray(),
            ]
        ]);
    }
}
