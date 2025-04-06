<?php

namespace App\ApiResource\User;

use App\Dto\User\UserCreateDTO;
use App\Dto\User\UserPatchDTO;
use App\Dto\User\UserReadDTO;
use App\Entity\User;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\Patch;
use App\State\User\UserReadProvider;
use ApiPlatform\Metadata\ApiResource;
use App\State\User\UserPatchProcessor;
use App\State\User\UserCreateProcessor;
use ApiPlatform\Doctrine\Orm\State\Options;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

#[ApiResource(
    shortName: 'User',
    stateOptions: new Options(
        entityClass: User::class,
    ),
    operations: [
        new Post(
            uriTemplate: '/user',
            input: UserCreateDTO::class,
            processor: UserCreateProcessor::class,
            name: 'userPost',
            security: "is_granted('PUBLIC_ACCESS')",
            description: 'Créer un nouvel utilisateur avec un éventuel code de parrainage',
            denormalizationContext: [
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            openapi: new Model\Operation(
                summary: 'Inscription d’un nouvel utilisateur',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'email' => ['type' => 'string', 'example' => 'test@example.com'],
                                    'password' => ['type' => 'string', 'example' => 'MotDePasseSecure123!'],
                                    'pseudo' => ['type' => 'string', 'example' => 'PseudoCool'],
                                    'referralCode' => ['type' => 'string', 'example' => 'ABC123XYZ']
                                ],
                                'required' => ['email', 'password', 'pseudo']
                            ])
                        )
                    ])
                ),
                responses: [
                    '201' => [
                        'description' => 'Utilisateur créé avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Utilisateur créé avec succès']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Get(
            uriTemplate: '/user/{id}',
            provider: UserReadProvider::class,
            output: UserReadDTO::class,
            name: 'userGet',
            security: "is_granted('IS_AUTHENTICATED_FULLY') and user.getId() == request.attributes.get('id')",
            normalizationContext: [
                'groups' => ['read:user'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            description: 'Récupérer les informations de l’utilisateur connecté',
            openapi: new Model\Operation(
                summary: 'Récupération des infos de l’utilisateur connecté',
                responses: [
                    '200' => [
                        'description' => 'Données de l’utilisateur',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'id' => ['type' => 'string', 'format' => 'uuid'],
                                        'email' => ['type' => 'string', 'example' => 'test@example.com'],
                                        'pseudo' => ['type' => 'string', 'example' => 'PseudoCool'],
                                        'balance' => ['type' => 'number', 'example' => 100],
                                        'createdAt' => ['type' => 'string', 'format' => 'date-time'],
                                        'updatedAt' => ['type' => 'string', 'format' => 'date-time'],
                                        'lastLaunch' => ['type' => 'string', 'format' => 'date-time', 'nullable' => true],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        ),
        new Patch(
            uriTemplate: '/user/{id}',
            input: UserPatchDTO::class,
            processor: UserPatchProcessor::class,
            name: 'userPatch',
            security: "is_granted('IS_AUTHENTICATED_FULLY') and user.getId() == request.attributes.get('id')",
            denormalizationContext: [
                'groups' => ['patch:user'],
                AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
            ],
            description: 'Modifier son pseudo ou son mot de passe',
            openapi: new Model\Operation(
                summary: 'Mettre à jour le pseudo ou mot de passe de l’utilisateur connecté',
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'application/merge-patch+json' => new Model\MediaType(
                            schema: new \ArrayObject([
                                'type' => 'object',
                                'properties' => [
                                    'pseudo' => ['type' => 'string', 'example' => 'NewPseudoCool'],
                                    'currentPassword' => ['type' => 'string', 'example' => 'AncienMotDePasse123!'],
                                    'newPassword' => ['type' => 'string', 'example' => 'NouveauMotDePasseUltraSecure456@']
                                ]
                            ])
                        )
                    ])
                ),
                responses: [
                    '200' => [
                        'description' => 'Utilisateur mis à jour avec succès',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'message' => ['type' => 'string', 'example' => 'Utilisateur mis à jour avec succès']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    '400' => [
                        'description' => 'Erreur de validation ou mot de passe incorrect',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'error' => ['type' => 'string', 'example' => 'Le mot de passe actuel est requis pour changer le mot de passe.']
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        )
    ]
)]
class UserResource {}
