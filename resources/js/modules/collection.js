/*
 * PhpMongoAdmin (www.phpmongoadmin.com) by Masterforms Mobile & Web (MFMAW)
 * @version      collection.js 1001 6/8/20, 8:58 pm  Gilbert Rehling $
 * @package      PhpMongoAdmin\resources
 * @subpackage   collection.js
 * @link         https://github.com/php-mongo/admin PHP MongoDB Admin
 * @copyright    Copyright (c) 2020. Gilbert Rehling of MMFAW. All rights reserved. (www.mfmaw.com)
 * @licence      PhpMongoAdmin is an Open Source Project released under the GNU GPLv3 license model.
 * @author       Gilbert Rehling:  gilbert@phpmongoadmin.com (www.gilbert-rehling.com)
 *  php-mongo-admin - License conditions:
 *  Contributions to our suggestion box are welcome: https://phpmongotools.com/suggestions
 *  This web application is available as Free Software and has no implied warranty or guarantee of usability.
 *  See licence.txt for the complete licensing outline.
 *  See https://www.gnu.org/licenses/license-list.html for information on GNU General Public License v3.0
 *  See COPYRIGHT.php for copyright notices and further details.
 */

/*
* ----------------------------------------------------
* VUEX modules/collection.js
* ----------------------------------------------------
* The Vuex data store for collection component views
*/

/*
*   Fetch the API to handle the requests
*/
import CollectionApi from '../api/collection.js'

/*
*   Imports the Event Bus to pass events on tag updates
*/
import { EventBus } from '../event-bus.js';

export const collection = {
    /*
    *   Defines the 'state' being monitored for the module
    */
    state: {
        activeCollection: null,
        collection: {},
        collectionLoadStatus: 0,
        collections: [],
        collectionsLoadStatus: 0,
        currentFormat: 'json',
        displayCollection: {},
        displayCollectionStatus: 0,
        createCollectionStatus: 0,
        deleteCollectionStatus: 0,
        documentUpdateStatus: 0,
        errorData: {}
    },

    /*
    *   Defines the actions available for the collection module
    */
    actions: {
        /*
        *   Loads the collection from the API
        */
        loadCollections( { commit, rootState, dispatch } ) {
            commit( 'setCollectionsLoadStatus', 1 );

            CollectionApi.getCollections()
                .then( ( response ) => {
                    commit( 'setCollections', response.data.data.collections );
                    commit( 'setCollectionsLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setCollections', [] );
                    commit( 'setCollectionsLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No collections were returned from the api - please try again later' });
                });
        },

        /*
        *   Loads a collection from the API
        */
        loadCollection( { commit, rootState, dispatch }, data ) {
            commit( 'setCollectionLoadStatus', 1 );

            CollectionApi.getCollection( data )
                .then( ( response ) => {
                    commit( 'setCollection', response.data.data.collection );
                    commit( 'setCollectionLoadStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setCollection', {} );
                    commit( 'setCollectionLoadStatus', 3 );
                    console.log(error);
                    EventBus.$emit('no-results-found', { notification: 'No collection was returned from the api - please try again later' });
                });
        },

        /*
        *   Create a new collection - add result to collection array
        */
        createCollection( { commit, rootState, dispatch }, data ) {
            commit( 'setCreateCollectionStatus', 1);

            CollectionApi.createCollection( data )
                .then( ( response ) => {
                    commit( 'setCreateCollectionStatus', 2 );
                    commit( 'setCreatedCollection', response.data.data.collection );
                    dispatch( 'setCollection', response.data.data.collection );
                })
                .catch( (error) => {
                    commit( 'setCreateCollectionStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        /*
        *   Delete one or more collections - remove collection from array
        */
        deleteCollection( { commit, rootState, dispatch }, data ) {
            commit( 'setDeleteCollectionStatus', 1);

            CollectionApi.deleteCollection( data )
                .then( ( response ) => {
                    commit( 'setDeletedCollection', data );
                    commit( 'setDeleteCollectionStatus', 2 );
                })
                .catch( (error) => {
                    commit( 'setDeleteCollectionStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        setDbCollections( { commit }, data) {
            commit( 'setCollections', data);
        },

        /*
        *   Set the active collection - used for collection tracking
        */
        setActiveCollection( { commit }, data ) {
            console.log("setting active collection: " + data);
            commit( 'setActiveCollection', data );
        },

        /*
        *   Set the current format for data entry (json | array)
        */
        setCurrentFormat( { commit }, data ) {
            commit( 'setCurrentFormat', data );
        },

        /*
        *  Update a document within the collection
        */
        updateDocument( { commit, rootStore, dispatch }, data ) {
            commit( 'setUpdateDocumentStatus', 1);

            CollectionApi.updateDocument( data )
                .then( (response) => {
                    commit( 'setUpdatedDocument', data );
                    commit( 'setUpdateDocumentStatus', 2);
                })
                .catch( (error) => {
                    commit( 'setUpdateDocumentStatus', 3);
                    commit( 'setErrorData', error);
                    console.log(error);
                });
        },

        setDocument( { commit }, data ) {
            console.log("set document: " + data);
            commit( 'setUpdatedDocument', data);
        }
    },

    /*
    *   Defines the mutations used for the collection module
    */
    mutations: {
        /*
        *   Set the collection load status
        */
        setCollectionsLoadStatus( state, status ) {
            state.collectionLoadStatus = status;
        },

        /*
        *   Sets the collections
        */
        setCollections( state, collections ) {
            state.collections = collections;
        },

        /*
        *   Set the collection load status
        */
        setCollectionLoadStatus( state, status ) {
            state.collectionLoadStatus = status;
        },

        /*
        *   Sets the collection
        */
        setCollection( state, collection ) {
            // console.log("saving collection: " + collection);
            state.collection = collection;
        },

        /*
        *   Set the display collection
        */
        setDisplayCollection( state, collection) {
            state.displayCollection = collection;
        },

        /*
        *   Set the display collection status
        */
        setDisplayCollectionStatus( state, status) {
            state.displayCollectionStatus = status;
        },

        /*
        *   Set the create collection status
        */
        setCreateCollectionStatus( state, status) {
            state.createCollectionStatus = status;
        },

        /*
        *   Add the new collection into the existing array
        */
        setCreatedCollection( state, collection ) {
            state.collections.push( collection );
        },

        /*
         *  Set the document update status
         */
        setUpdateDocumentStatus( state, status ) {
            state.documentUpdateStatus = status;
        },

        /*
         * Replace document with updated version
         */
        setUpdatedDocument( state, data ) {
            console.log("mute data index: " + data.index);
            if (data.text) {
                console.log("mute data text: " + data.text);
                console.log("mute data data: " + data.data);
                state.collection.objects.objects[data.index].text = data.text;
                state.collection.objects.objects[data.index].data = data.data;
            } else {
                console.log("mute data document raw: " + state.collection.objects.objects[data.index].raw);
                state.collection.objects.objects[data.index].raw = JSON.parse(data.document);
            }
        },

        /*
        *   Set the delete collection status
        */
        setDeleteCollectionStatus( state, status) {
            state.deleteCollectionStatus = status;
        },

        /*
        *   Set (remove) the deleted collection(s) from the existing array
        */
        setDeletedCollection( state, collections ) {
            collections.forEach(function(value, index) {
                let arr = [];
                state.collections.forEach(function(db, index) {
                    if (db.db.name !== value) {
                        arr.push(db);
                    }
                });
                state.collections = arr;
            });
        },

        /*
        *   Save the error data for reference
        */
        setErrorData( state, error ) {
            state.errorData = error;
        },

        /*
        *   Set the active collection
        */
        setActiveCollection(state, collection) {
            state.activeCollection = collection;
        },

        /*
        *   Set the current format
        */
        setCurrentFormat(state, format) {
            state.currentFormat = format;
        }
    },

    /*
    *   Define the getters used by the collection module
    */
    getters: {
        /*
        *   Return the collections load status
        */
        getCollectionsLoadStatus( state ) {
            return state.collectionsLoadStatus;
        },

        /*
        *   Return the collections
        */
        getCollections( state ) {
            return state.collections;
        },

        /*
        *   Return the collection load status
        */
        getCollectionLoadStatus( state ) {
            return state.collectionLoadStatus;
        },

        /*
        *   Return the collection
        */
        getCollection( state ) {
            return state.collection;
        },

        /*
        *   Return the display collection status
        */
        getDisplayCollectionStatus( state ) {
            return state.displayCollectionStatus;
        },

        /*
        *   Return the display collection
        */
        getDisplayCollection: (state) => (id) => {
            console.log("getDisplayCollection: " + id);
            if (state.collection && state.collection.id !== '') {
                console.log("collection found!!");
                return state.collection;

            } else {
                let collection = state.collection.find(collection => collection.id === id);
                if (collection) {
                    console.log("setting display collection state: " + id);
                    state.displayCollectionStatus = id;
                    state.displayCollection = collection;
                    return collection;
                }
            }
        },

        /*
        *   Get the create collection status
        */
        getCreateCollectionStatus( state ) {
            return state.createCollectionStatus;
        },

        /*
        *   Get the delete collection status
        */
        getDeleteCollectionStatus( state ) {
            return state.deleteCollectionStatus;
        },

        /*
         *  Get the document update status
         */
        getUpdateDocumentStatus (state ) {
            return state.documentUpdateStatus;
        },

        /*
        *   Get the stats array (object) from the collection object
        */
        getCollectionStats( state ) {
            if (state.collection) {
                return state.collection.stats;
            }
        },

        /*
        *   Fetch any active error
        */
        getCollectionErrorData( state ) {
            return state.errorData;
        },

        /*
        *   Get the active collection
        */
        getActiveCollection( state) {
            return state.activeCollection;
        },

        /*
        *   Det the current format
        */
        getCurrentFormat( state ) {
            return state.currentFormat;
        },

        /*
         *  Get a document from the current collection
         */
        getDocument: ( state ) => ( id ) => {
            let documents = state.collection.objects.objects;
            if (documents) {
                return documents.map( document => {
                    return document._id = id;
                });
            }
        }
    }
};
