/**
 * @license LGPLv3, http://opensource.org/licenses/LGPL-3.0
 * @copyright Aimeos (aimeos.org), 2017-2023
 */


$(function() {
    Aimeos.Emoji.init();
});


Aimeos.Emoji = {
    init() {
        Aimeos.components['emoji'] = new Vue({
            el: document.querySelector('#emoji'),
            data: {
                emojiCount: $(".emojiCountData").data("emoji")?.emojiCount || 0,
            },
            mixins: [this.mixins]
        });
    },

    mixins: {
        methods: {
            remove() {
                this.emojiCount = 0
            },
        }
    }
};
