/*
*   Import Vue
*/
import Vue from 'vue';

Vue.mixin( {
    beforeCreate() {
        const options = this.$options;
        if ( options.jqf )
            this.$jqf = options.jqf;

        else if ( options.parent && options.parent.$jqf )
            this.$jqf = options.parent.$jqf;
    }
});

/**
 * Class handling function to mock jQuery behavior
 *
 */
export default function makeJqf() {
    /**
     * Returns a set of methods for manipulating classes and actions on DOM elements
     */
    return function jqf(element) {
        let list = element.classList;

        return {
            toggle: function (c) {
                list.toggle(c);
                return this;
            },
            add: function (c) {
                list.add(c);
                return this;
            },
            remove: function (c) {
                list.remove(c);
                return this;
            },
            replace: function (array) {
                list.remove(array[0]);
                list.add(array[1]);
                return this;
            },
            hasClass: function (c) {
                console.log(list);
                console.log(c);
                return list.forEach(function(value, index) {
                    console.log("checking: " + value);
                    if (value === c) {
                        console.log("found it!!");
                        return true;
                    }
                });
            },
            text: function (t) {
                element.innerText = t;
            },
            html: function (h) {
                element.innerHTML = h;
            },
            focus: function () {
                element.focus();
            },
            show: function () {
                element.style.display = 'inline-block';
            },
            hide: function() {
                element.style.display = 'none';
            },
            css: function(e, v) {
                element.style[e] = v;
            }
        };
    }
}
