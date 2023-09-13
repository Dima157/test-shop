<?php

/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2015-2023
 */

$enc = $this->encoder();


?>
<div id="emoji" class="item-emoji tab-pane fade" role="tabpanel" aria-labelledby="emoji">
    <div class="emojiCountData"
         data-emoji="<?= $enc->attr( $this->get( 'emojiData', [] )) ?>"
    >
        <input class="item-type" type="hidden"
               name="emojiCount"
               :value="emojiCount">
        <input class="emoji-count-input stock-timeframe optional form-control item-timeframe" v-model="emojiCount" :value="emojiCount" readonly>
        <div class="actions">
            <div tabindex="8" title="Delete this entry" class="btn act-delete fa" v-on:click="remove()"></div>
        </div>
    </div>
</div>
