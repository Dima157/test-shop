<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 * @package Client
 * @subpackage JsonApi
 */


namespace Aimeos\Client\JsonApi\Emoji;

use App\Models\Emojis;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Laminas\Diactoros\StreamFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;


/**
 * JSON API standard client
 *
 * @package Client
 * @subpackage JsonApi
 */
class Standard
	extends \Aimeos\Client\JsonApi\Base
	implements \Aimeos\Client\JsonApi\Iface
{
	/** client/jsonapi/attribute/name
	 * Class name of the used attribute client implementation
	 *
	 * Each default JSON API client can be replace by an alternative imlementation.
	 * To use this implementation, you have to set the last part of the class
	 * name as configuration value so the client factory knows which class it
	 * has to instantiate.
	 *
	 * For example, if the name of the default class is
	 *
	 *  \Aimeos\Client\JsonApi\Emoji\Standard
	 *
	 * and you want to replace it with your own version named
	 *
	 *  \Aimeos\Client\JsonApi\Emoji\Myattribute
	 *
	 * then you have to set the this configuration option:
	 *
	 *  client/jsonapi/attribute/name = Myattribute
	 *
	 * The value is the last part of your own class name and it's case sensitive,
	 * so take care that the configuration value is exactly named like the last
	 * part of the class name.
	 *
	 * The allowed characters of the class name are A-Z, a-z and 0-9. No other
	 * characters are possible! You should always start the last part of the class
	 * name with an upper case character and continue only with lower case characters
	 * or numbers. Avoid chamel case names like "MyAttribute"!
	 *
	 * @param string Last part of the class name
	 * @since 2017.03
	 * @category Developer
	 */

	/** client/jsonapi/attribute/decorators/excludes
	 * Excludes decorators added by the "common" option from the JSON API clients
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to remove a decorator added via
	 * "client/jsonapi/common/decorators/default" before they are wrapped
	 * around the JsonApi client.
	 *
	 *  client/jsonapi/decorators/excludes = array( 'decorator1' )
	 *
	 * This would remove the decorator named "decorator1" from the list of
	 * common decorators ("\Aimeos\Client\JsonApi\Common\Decorator\*") added via
	 * "client/jsonapi/common/decorators/default" for the JSON API client.
	 *
	 * @param array List of decorator names
	 * @since 2017.07
	 * @category Developer
	 * @see client/jsonapi/common/decorators/default
	 * @see client/jsonapi/attribute/decorators/global
	 * @see client/jsonapi/attribute/decorators/local
	 */

	/** client/jsonapi/attribute/decorators/global
	 * Adds a list of globally available decorators only to the JsonApi client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap global decorators
	 * ("\Aimeos\Client\JsonApi\Common\Decorator\*") around the JsonApi
	 * client.
	 *
	 *  client/jsonapi/attribute/decorators/global = array( 'decorator1' )
	 *
	 * This would add the decorator named "decorator1" defined by
	 * "\Aimeos\Client\JsonApi\Common\Decorator\Decorator1" only to the
	 * "attribute" JsonApi client.
	 *
	 * @param array List of decorator names
	 * @since 2017.07
	 * @category Developer
	 * @see client/jsonapi/common/decorators/default
	 * @see client/jsonapi/attribute/decorators/excludes
	 * @see client/jsonapi/attribute/decorators/local
	 */

	/** client/jsonapi/attribute/decorators/local
	 * Adds a list of local decorators only to the JsonApi client
	 *
	 * Decorators extend the functionality of a class by adding new aspects
	 * (e.g. log what is currently done), executing the methods of the underlying
	 * class only in certain conditions (e.g. only for logged in users) or
	 * modify what is returned to the caller.
	 *
	 * This option allows you to wrap local decorators
	 * ("\Aimeos\Client\JsonApi\Emoji\Decorator\*") around the JsonApi
	 * client.
	 *
	 *  client/jsonapi/attribute/decorators/local = array( 'decorator2' )
	 *
	 * This would add the decorator named "decorator2" defined by
	 * "\Aimeos\Client\JsonApi\Emoji\Decorator\Decorator2" only to the
	 * "attribute" JsonApi client.
	 *
	 * @param array List of decorator names
	 * @since 2017.07
	 * @category Developer
	 * @see client/jsonapi/common/decorators/default
	 * @see client/jsonapi/attribute/decorators/excludes
	 * @see client/jsonapi/attribute/decorators/global
	 */
    private \App\Services\Emojis $emojiService;
    public function __construct(\Aimeos\MShop\ContextIface $context)
    {
        parent::__construct($context);
        $this->emojiService = new \App\Services\Emojis();
    }

    /**
	 * Returns the resource or the resource list
	 *
	 * @param \Psr\Http\Message\ServerRequestInterface $request Request object
	 * @param \Psr\Http\Message\ResponseInterface $response Response object
	 * @return \Psr\Http\Message\ResponseInterface Modified response object
	 */
	public function get( ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
	{
        $data = Validator::make(['productId' => $request->getQueryParams()['productId']],[
            'productId' => 'required|int'
        ]);
        if ($data->fails() || Auth::guest()) {
            $emojis = $this->emojiService->emojiList();
        } else {
            $emojis = $this->emojiService->productEmojis($request->getQueryParams()['productId']);
        }

		return $response->withHeader( 'Allow', 'GET,OPTIONS' )
			->withHeader( 'Cache-Control', 'max-age=300' )
			->withHeader( 'Content-Type', 'application/vnd.api+json' )
			->withBody( (new StreamFactory())->createStream(json_encode($emojis)))
			->withStatus( 200 );
	}


    /**
     * Creates or updates the resource or the resource list
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request Request object
     * @param \Psr\Http\Message\ResponseInterface $response Response object
     * @return \Psr\Http\Message\ResponseInterface Modified response object
     */
    public function post( ServerRequestInterface $request, ResponseInterface $response ) : \Psr\Http\Message\ResponseInterface
    {
        $this->assertLogIn();
        $data = Validator::make([
            'productId' => $request->getParsedBody()['productId'],
            'emojiId' => $request->getParsedBody()['emojiId']
        ],[
            'productId' => 'required|int',
            'emojiId' => 'required|int'
        ]);
        if ($data->fails()) {
            throw new BadRequestException($data->errors()->toArray());
        }

        $this->emojiService->addEmojiToProduct($request->getParsedBody()['productId'], $request->getParsedBody()['emojiId']);

        return $response->withHeader( 'Allow', 'GET,OPTIONS' )
            ->withHeader( 'Cache-Control', 'max-age=300' )
            ->withHeader( 'Content-Type', 'application/vnd.api+json' )
            ->withStatus( 200 );
    }

    private function assertLogIn() {
        if (Auth::guest()) {
            throw new \Aimeos\Client\JsonApi\Exception( 'Unauthorized', 4001 );
        }
    }
}
