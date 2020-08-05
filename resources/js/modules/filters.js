/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      filters.js 1001 6/8/20, 1:00 am  Gilbert Rehling $
 * @package      filters.js
 * @subpackage   Id
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is Open Source and is released under the MIT licence model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions via our suggestion box are welcome. https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See COPYRIGHT.php for copyright notices and further details.
 */

/*
|-------------------------------------------------------------------------------
| VUEX modules/filters.js
|-------------------------------------------------------------------------------
| The Vuex data store for the filters state
*/
export const filters = {
    /*
      Defines the state used by the module
    */
    state: {
        cityFilter: '',
        textSearch: '',
        orderBy: 'name',
        orderDirection: 'asc'
    },

    /*
    Defines the actions that can be performed on the state.
  */
    actions: {
        /*
        *   Updates the city filter.
        */
        updateCityFilter( { commit }, data ){
            commit( 'setCityFilter', data );
        },

        /*
          Updates the text search filter
        */
        updateSetTextSearch( { commit }, data ){
            commit( 'setTextSearch', data );
        },

        /*
          Updates the order by setting and sorts the cafes.
        */
        updateOrderBy( { commit, state, dispatch }, data ){
            commit( 'setOrderBy', data );
            dispatch( 'orderCafes', { order: state.orderBy, direction: state.orderDirection } );
        },

        /*
          Updates the order direction and sorts the cafes.
        */
        updateOrderDirection( { commit, state, dispatch }, data ){
            commit( 'setOrderDirection', data );
            dispatch( 'orderCafes', { order: state.orderBy, direction: state.orderDirection } );
        },

        /*
          Resets the filters
        */
        resetFilters( { commit }, data ){
            commit( 'resetFilters' );
        }
    },

    /*
    *   Defines the mutations used by the state.
    */
    mutations: {
        /*
          Sets the city filter.
        */
        setCityFilter( state, city ){
            state.cityFilter = city;
        },

        /*
          Sets the text search filter.
        */
        setTextSearch( state, search ){
            state.textSearch = search;
        },

        /*
          Sets the order by filter.
        */
        setOrderBy( state, orderBy ){
            state.orderBy = orderBy;
        },

        /*
          Sets the order direction filter.
        */
        setOrderDirection( state, orderDirection ){
            state.orderDirection = orderDirection;
        },

        /*
          Resets the active filters.
        */
        resetFilters( state ){
            state.cityFilter = '';
            state.textSearch = '';
            state.orderBy = 'created';
            state.orderDirection = 'desc';
        }
    },

    /*
    *   Defines the getters on the Vuex module.
    */
    getters: {
        /*
          Gets the city filter.
        */
        getCityFilter( state ){
            return state.cityFilter;
        },

        /*
          Gets the text search filter.
        */
        getTextSearch( state ){
            return state.textSearch;
        },

        /*
          Gets the order by filter.
        */
        getOrderBy( state ){
            return state.orderBy;
        },

        /*
          Gets the order direction filter.
        */
        getOrderDirection( state ){
            return state.orderDirection;
        }
    }
};
