<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Metaways Infosystems GmbH, 2014
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

/* Available data:
 * - productItem : Product item incl. referenced items
 */

$enc = $this->encoder();


/** client/html/catalog/actions/list
 * List of user action names that should be displayed in the catalog detail view
 *
 * Users can add products to several personal lists that are either only
 * available during the session or permanently if the user is logged in. The list
 * of pinned products is session based while the watch list and the favorite
 * products are durable. For the later two lists, the user has to be logged in
 * so the products can be associated to the user account.
 *
 * The order of the action names in the configuration determines the order of
 * the actions on the catalog detail page.
 *
 * @param array List of user action names
 * @since 2017.04
 */
$list = $this->config( 'client/html/catalog/actions/list', ['pin', 'watch', 'favorite'] );


?>
<style>
    .reaction {
        display: inline-block;
        position: relative;
        top: -5px
    }

    #emoji-list {
        position: absolute;
        top: 38px;
        left: 0;
        display: none;
    }

    #emoji-list .emoji {
        font-size: 20px;
        margin-right: 5px;
        cursor: pointer;
    }

    .emoji-item {
        font-size: 25px;
    }

    #emoji-list:hover {
        cursor: pointer;
    }
</style>
<div class="catalog-actions">
	<?php if( in_array( 'pin', $list ) ) : ?>
		<form class="actions-pin" method="POST" action="<?= $enc->attr( $this->link( 'client/html/catalog/session/pinned/url' ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'pin_action' ) ?>" value="add">
			<input type="hidden" name="<?= $this->formparam( 'pin_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_prodid' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>">
			<button class="actions-button actions-button-pin" title="<?= $enc->attr( $this->translate( 'client/code', 'pin' ) ) ?>"></button>
		</form><!--
	--><?php endif ?><!--

	--><?php if( in_array( 'watch', $list ) ) : ?>
		<form class="actions-watch" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/watch/url' ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'wat_action' ) ?>" value="add">
			<input type="hidden" name="<?= $this->formparam( 'wat_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_prodid' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>">
			<button class="actions-button actions-button-watch" title="<?= $enc->attr( $this->translate( 'client/code', 'watch' ) ) ?>"></button>
		</form><!--
	--><?php endif ?><!--

	--><?php if( in_array( 'favorite', $list ) ) : ?>
		<form class="actions-favorite" method="POST" action="<?= $enc->attr( $this->link( 'client/html/account/favorite/url' ) ) ?>">
			<!-- catalog.detail.csrf --><?= $this->csrf()->formfield() ?><!-- catalog.detail.csrf -->
			<input type="hidden" name="<?= $this->formparam( 'fav_action' ) ?>" value="add">
			<input type="hidden" name="<?= $this->formparam( 'fav_id' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_prodid' ) ?>" value="<?= $enc->attr( $this->productItem->getId() ) ?>">
			<input type="hidden" name="<?= $this->formparam( 'd_name' ) ?>" value="<?= $this->productItem->getName( 'url' ) ?>">
			<button class="actions-button actions-button-favorite" title="<?= $enc->attr( $this->translate( 'client/code', 'favorite' ) ) ?>"></button>
		</form>
	<?php endif ?>
    <div style="display: none" data-guest="<?= Auth::guest() ?>" data-prodid="<?= $enc->attr( $this->productItem->getId()) ?>" id="info"></div>
    <?php if(!Auth::guest()): ?>
        <div class="reaction">
            <div class="reaction-icon">
                <span class="emoji-item main-emoji"></span>
            </div>
            <div id="emoji-list">
            </div>
        </div>
    <?php endif; ?>
    <script>
        const info = document.getElementById('info');
        if (!info.dataset.guest) {
            (() => {
                fetch('/jsonapi/emoji?' + new URLSearchParams({productId: info.dataset.prodid}), {
                    cache  : "no-store",
                    headers: {
                        'Content-Type': 'application/json'
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        let mainEmoji = data.find(emoji => emoji?.isSelected);
                        if (!mainEmoji) {
                            mainEmoji = data[0];
                            data = data.slice(1)
                            const divElement = document.getElementById('emoji-list');
                            addEmojiToList(divElement, data)
                        }
                        document.querySelector('.main-emoji').innerHTML = unicodeToEmoji(mainEmoji.unicode)
                    })
            })()
            const reaction = document.querySelector('.reaction');
            const emojiList = document.getElementById('emoji-list');

            reaction.addEventListener('mouseover', function () {
                emojiList.style.display = 'block';
            });

            reaction.addEventListener('mouseout', function () {
                emojiList.style.display = 'none';
            });

            function addEmojiToList(targetTag, emojiItems) {
                for (const emoji of emojiItems) {
                    const newSpan = document.createElement('span');
                    newSpan.className = 'emoji-item';
                    emoji.unicode.replace('U+', '&#x');
                    newSpan.innerHTML = unicodeToEmoji(emoji.unicode);
                    newSpan.addEventListener("click", function () {
                        selectEmoji(emoji.id, emoji.unicode, info.dataset.proid)
                    }, false)
                    targetTag.appendChild(newSpan);
                }
            }

            function selectEmoji(emojiId, emojiUnicode, productId) {
                fetch('/jsonapi/emoji?' + new URLSearchParams({
                    _token: getMeta('csrf-token')
                }), {
                    method : 'POST',
                    body   : JSON.stringify({emojiId, productId: document.getElementById('info').dataset.prodid}),
                    headers: {
                        'Content-Type': 'application/json'
                    }
                }).then(res => {
                    document.querySelector('.main-emoji').innerHTML = unicodeToEmoji(emojiUnicode);
                    document.getElementById('emoji-list').remove()
                })
            }

            function getMeta(metaName) {
                const metas = document.getElementsByTagName('meta');

                for (let i = 0; i < metas.length; i++) {
                    if (metas[i].getAttribute('name') === metaName) {
                        return metas[i].getAttribute('content');
                    }
                }

                return '';
            }

            function unicodeToEmoji(unicode) {
                return unicode.replace('U+', '&#x');
            }
        }
    </script>
</div>
