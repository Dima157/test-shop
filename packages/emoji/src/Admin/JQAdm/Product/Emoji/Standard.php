<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 * @package Admin
 * @subpackage JQAdm
 */


namespace Aimeos\Admin\JQAdm\Product\Emoji;

use App\Services\Emojis;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

sprintf( 'emoji' ); // for translation


/**
 * Default implementation of product bundle JQAdm client.
 *
 * @package Admin
 * @subpackage JQAdm
 */
class Standard
    extends \Aimeos\Admin\JQAdm\Common\Admin\Factory\Base
    implements \Aimeos\Admin\JQAdm\Common\Admin\Factory\Iface
{
    /** admin/jqadm/product/bundle/name
     * Name of the bundle subpart used by the JQAdm product implementation
     *
     * Use "Myname" if your class is named "\Aimeos\Admin\Jqadm\Product\E\Myname".
     * The name is case-sensitive and you should avoid camel case names like "MyName".
     *
     * @param string Last part of the JQAdm class name
     * @since 2016.04
     */

    private Emojis $emojiService;
    public function __construct(\Aimeos\MShop\ContextIface $context)
    {
        parent::__construct($context);
        $this->emojiService = new Emojis();
    }


    /**
     * Returns a single resource
     *
     * @return string|null HTML output
     */
    public function get() : ?string
    {
        $view = $this->object()->data( $this->view() );

        $view->emojiData = $this->toArray( $view->item );
        $view->emojiBody = parent::get();

        return $this->render( $view );
    }


    /**
     * Saves the data
     *
     * @return string|null HTML output
     */
    public function save() : ?string
    {
        $view = $this->view();
        if ($view->param( 'emojiCount', 0 ) == 0) {
            $this->emojiService->removeEmojisToProduct($view->item->getId());
        }

        return null;
    }

    /**
     * Constructs the data array for the view from the given item
     *
     * @param \Aimeos\MShop\Product\Item\Iface $item Product item object including referenced domain items
     * @param bool $copy True if items should be copied, false if not
     * @return string[] Multi-dimensional associative list of item data
     */
    protected function toArray( \Aimeos\MShop\Product\Item\Iface $item, bool $copy = false ) : array
    {
        return ['emojiCount' => $this->emojiService->getProductEmojisCount($item->getId())];
    }


    /**
     * Returns the rendered template including the view data
     *
     * @param \Aimeos\Base\View\Iface $view View object with data assigned
     * @return string HTML output
     */
    protected function render( \Aimeos\Base\View\Iface $view ) : string
    {
        /** admin/jqadm/product/bundle/template-item
         * Relative path to the HTML body template of the bundle subpart for products.
         *
         * The template file contains the HTML code and processing instructions
         * to generate the result shown in the body of the frontend. The
         * configuration string is the path to the template file relative
         * to the templates directory (usually in templates/admin/jqadm).
         *
         * You can overwrite the template file configuration in extensions and
         * provide alternative templates. These alternative templates should be
         * named like the default one but with the string "default" replaced by
         * an unique name. You may use the name of your project for this. If
         * you've implemented an alternative client class as well, "default"
         * should be replaced by the name of the new class.
         *
         * @param string Relative path to the template creating the HTML code
         * @since 2016.04
         */
        $tplconf = 'admin/jqadm/product/emoji/template-item';
        $default = 'product/item-emoji';

        return $view->render( $view->config( $tplconf, $default ) );
    }
}
