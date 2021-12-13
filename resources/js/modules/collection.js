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
        clearCollectionStatus: 0,
        clearCollectionCount: 0,
        collection: {},
        collectionLoadStatus: 0,
        collections: [],
        collectionsLoadStatus: 0,
        collectionPropertiesStatus: 0,
        collectionRenameStatus: 0,
        collectionDuplicateStatus: 0,
        collectionIndexStatus: 0,
        collectionIndex: null,
        collectionValidationStatus: 0,
        collectionValidation: null,
        createCollectionStatus: 0,
        currentFormat: 'json',
        displayCollection: {},
        displayCollectionStatus: 0,
        deleteCollectionStatus: 0,
        deletingCollection: null,
        deleteCollectionCount: 0,
        documentCreateStatus: 0,
        documentUpdateStatus: 0,
        documentDeleteStatus: 0,
        documentDuplicateStatus: 0,
        errorData: {},
        exportCollectionStatus: 0,
        exportData: null,
        importCollectionStatus: 0,
        queryLogs: [],
        queryLogsLoadStatus: 0,
        queryExplain: {},
        queryExplainStatus: 0,
        queryCollection: [],
        queryCollectionLoadStatus: 0,
    },

    /*
    *   Defines the actions available for the collection module
    */
    actions: {
        /*
        *   Loads all collections from the API - needs a database reference
        *   ToDo: this method is unused - do we need it??
        */
        loadCollections( { dispatch, commit } ) {
            commit( 'setCollectionsLoadStatus', 1 );

            CollectionApi.getCollections()
                .then( ( response ) => {
                    commit( 'setCollections', response.data.data.collections );
                    commit( 'setCollectionsLoadStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setCollections', [] );
                    commit( 'setCollectionsLoadStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON());
                    EventBus.$emit('no-results-found', { notification: 'No collections were returned from the api - please try again later', timer: 5000 })
                })
        },

        /*
        *   Loads a collection from the API
        */
        loadCollection( { dispatch, commit, getters }, data ) {
            commit( 'setCollectionLoadStatus', 1 );

            CollectionApi.getCollection( data )
                .then( ( response ) => {
                    commit( 'setCollection', response.data.data.collection );
                    commit( 'setCollectionLoadStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setCollection', {} );
                    dispatch( 'setErrorData', error.response.data );
                    if (getters.isUserDbAdmin === true) {
                        dispatch( 'getLocalCollection', data );
                        return
                    }
                    commit( 'setErrorData', error.response);
                    commit( 'setCollectionLoadStatus', 3 );
                    console.log(error.toJSON());
                    EventBus.$emit('no-results-found', { notification: 'No collection was returned from the api - please try again later', timer: 5000 })
                })
        },

        /*
        *   Create a new collection - add result to collection array
        */
        createCollection( { dispatch, commit }, data ) {
            commit( 'setCreateCollectionStatus', 1);

            CollectionApi.createCollection( data )
                .then( ( response ) => {
                    dispatch( 'setCollection', response.data.data.collection )
                    commit( 'setCreateCollectionStatus', 2 );
                    //commit( 'setCreatedCollection', response.data.data.collection ); // this is triggered from a DB context
                })
                .catch( (error) => {
                    commit( 'setCreateCollectionStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        /*
        *   Query a collection - add result to queryResults array
        */
        queryCollection( { dispatch, commit }, data ) {
            commit( 'setQueryCollectionLoadStatus', 1);

            CollectionApi.queryCollection( data )
                .then( ( response ) => {
                    commit( 'setQueryCollection', response.data.data.documents );
                    commit( 'setQueryCollectionLoadStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setQueryCollectionLoadStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        /*
        *   Delete one or more collections - remove collection from array
        */
        deleteCollection( { dispatch, commit }, data ) {
            commit( 'setDeleteCollectionStatus', 1);
            commit( 'setDeletingCollection', data.collection );

            CollectionApi.deleteCollection( data )
                .then( () => {
                    dispatch('dropCollection', data);
                    commit( 'setDeleteCollectionStatus', 2 );
                    commit( 'setDeletingCollection', null )
                })
                .catch( (error) => {
                    commit( 'setDeleteCollectionStatus', 3 );
                    commit( 'setDeletingCollection', null );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        /*
        *   Clear all documents from one collection -  clear all cached doc objects
        */
        clearCollection( { dispatch, commit }, data ) {
            commit( 'setClearCollectionStatus', 1);

            CollectionApi.clearCollection( data )
                .then( ( response ) => {
                    commit( 'setClearedCollection', response.data.data.status );
                    commit( 'setClearCollectionStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setClearCollectionStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        exportCollection( { dispatch, commit }, data ) {
            commit( 'setExportCollectionStatus', 1);

            if (data.params.download === true) {
                CollectionApi.exportCollectionDownload( data )
                    .then( (response ) => {
                        if (response.data.success === false) {
                            commit( 'setErrorData', response.data.errors);
                            commit( 'setExportCollectionStatus', 3 )

                        } else {
                            commit( 'setExportCollectionStatus', 2 );
                            let blob        = response.data;
                            let ext = 'js';
                            if (data.params.gzip === true) {
                                ext = 'gz'
                            }
                            if (data.params.json === true) {
                                ext = 'json'
                            }
                            let date        = new Date();
                            let ts          = "_" + date.getTime();
                            let dt          = "_" + date.getFullYear() + parseInt(date.getMonth() + 1) + date.getDate();
                            let fileName    = "mongodb-" + data.database + dt + ts + "." + ext;
                            let link        = document.createElement('a');

                            link.href       = window.URL.createObjectURL(blob);
                            link.download   = fileName.replace(" ", "_").replace("+", "");
                            link.click()
                        }
                    })
                    .catch( (error) => {
                        commit( 'setExportCollectionStatus', 3 );
                        commit( 'setErrorData', error.response);
                        dispatch( 'setErrorData', error.response.data );
                        console.log(error.toJSON())
                    })

            } else {
                CollectionApi.exportCollectionView( data )
                    .then( (response ) => {
                        if (response.data.success === true) {
                            commit( 'setExportData', response.data.data.export);
                            commit( 'setExportCollectionStatus', 2 )

                        } else {
                            commit( 'setErrorData', response.data.errors);
                            commit( 'setExportCollectionStatus', 3 );
                            console.log(response.data.errors)
                        }
                    })
                    .catch( (error) => {
                        commit( 'setExportCollectionStatus', 3 );
                        commit( 'setErrorData', error.response);
                        dispatch( 'setErrorData', error.response.data );
                        console.log(error.toJSON())
                    })
            }
        },

        importCollection( { commit, dispatch }, data ) {
            commit('setImportCollectionStatus', 1);

            CollectionApi.importCollection( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        dispatch('loadDatabase', data.database);
                        commit('setImportCollectionStatus', 2)

                    } else {
                        commit( 'setErrorData', response.data.errors);
                        commit('setImportCollectionStatus', 3);
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit('setImportCollectionStatus', 3);
                    commit( 'setErrorData', error.toJSON().message);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        saveCollectionProperties( { dispatch, commit }, data) {
            commit('setCollectionPropertiesStatus', 1);

            CollectionApi.saveProperties( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        commit('setCollection', response.data.data.collection);
                        commit('setCollectionPropertiesStatus', 2)

                    } else {

                        commit( 'setErrorData', response.data.errors);
                        commit('setCollectionPropertiesStatus', 3);
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit('setCollectionPropertiesStatus', 3);
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        saveCollectionIndex( { dispatch, commit }, data) {
            commit('setCollectionIndexStatus', 1);
            commit( 'setCollectionIndex', null );

            CollectionApi.saveIndex( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        commit( 'setCollectionIndex', response.data.data.index );
                        commit( 'setCollectionIndexStatus', 2 )

                    } else {
                        commit( 'setErrorData', response.data.errors );
                        commit( 'setCollectionIndexStatus', 3 );
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit( 'setCollectionIndexStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        renameCollection( { dispatch, commit }, data) {
            commit('setCollectionRenameStatus', 1);

            CollectionApi.renameCollection( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        dispatch( 'loadDatabase', data.database );
                        dispatch( 'setActiveCollection', data.params.newName );
                        commit( 'setCollectionRenameStatus', 2 )

                    } else {
                        commit( 'setErrorData', response.data.errors );
                        commit( 'setCollectionRenameStatus', 3 );
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit( 'setCollectionRenameStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        duplicateCollection( { dispatch, commit }, data) {
            commit('setCollectionDuplicateStatus', 1);

            CollectionApi.duplicateCollection( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        dispatch( 'loadDatabase', data.database );
                        dispatch( 'setActiveCollection', data.params.duplicateName );
                        dispatch( 'loadCollection', { database: data.database, collection: data.params.duplicateName } );
                        commit( 'setCollectionDuplicateStatus', 2 )

                    } else {
                        commit( 'setErrorData', response.data.errors );
                        commit( 'setCollectionDuplicateStatus', 3 );
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit( 'setCollectionDuplicateStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        validateCollection( { dispatch, commit }, data) {
            commit('setCollectionValidationStatus', 1);

            CollectionApi.validateCollection( data )
                .then( (response) => {
                    if (response.data.success === true) {
                        commit( 'setCollectionValidation', response.data.data.validation[0] );
                        commit( 'setCollectionValidationStatus', 2 )

                    } else {
                        commit( 'setErrorData', response.data.errors );
                        commit( 'setCollectionValidationStatus', 3 );
                        console.log(response.data.errors)
                    }
                })
                .catch( (error) => {
                    commit( 'setCollectionValidationStatus', 3 );
                    commit( 'setErrorData', error.response);
                    dispatch( 'setErrorData', error.response.data );
                    console.log(error.toJSON())
                })
        },

        /*
         *  Get the query logs for a database.collection - displays in a modal
         */
        getQueryLogs( { commit }, data) {
            commit( 'setQueryLogsLoadStatus', 1 );

            CollectionApi.getQueryLogs( data )
                .then( ( response ) => {
                    commit( 'setQueryLogs', response.data.data.logs );
                    commit( 'setQueryLogsLoadStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setQueryLogs', [] );
                    commit( 'setQueryLogsLoadStatus', 3 );
                    console.log(error)
                })
        },

        /*
         *  Get the query explain results - displays in a modal
         */
        getQueryExplain( { commit }, data) {
            commit( 'setQueryExplainStatus', 1 );

            CollectionApi.getQueryExplain( data )
                .then( ( response ) => {
                    commit( 'setQueryExplain', response.data.data.explain );
                    commit( 'setQueryExplainStatus', 2 )
                })
                .catch( (error) => {
                    commit( 'setQueryExplain', [] );
                    commit( 'setQueryExplainStatus', 3 );
                    console.log(error)
                })
        },

        /*
        *  Update a document within the collection
        */
        updateDocument( { commit }, data ) {
            commit( 'setUpdateDocumentStatus', 1);

            CollectionApi.updateDocument( data )
                .then( (response) => {
                    if (response.data.message === 'success') {
                        commit( 'setUpdatedDocument', data );
                        commit( 'setUpdateDocumentStatus', 2);
                    } else {
                        commit( 'setUpdateDocumentStatus', 3);
                        commit( 'setErrorData', 'no result')
                    }
                })
                .catch( (error) => {
                    commit( 'setUpdateDocumentStatus', 3);
                    commit( 'setErrorData', error);
                    console.log(error)
                })
        },

        /*
        *  Duplicate a document within the collection
        *  This method and 'createDocument' are basically synonymous
        */
        duplicateDocument( { commit }, data ) {
            commit( 'setDuplicateDocumentStatus', 1);

            CollectionApi.duplicateDocument( data )
                .then( (response) => {
                    if (response.data.message === 'success') {
                        commit( 'setCreatedDocument', response.data.data.document );
                        commit( 'setDuplicateDocumentStatus', 2);
                    } else {
                        commit( 'setDuplicateDocumentStatus', 3);
                        let error = response.data.data ? response.data.data : 'An unhandled error occurred';
                        commit( 'setErrorData', error)
                    }
                })
                .catch( (error) => {
                    commit( 'setDuplicateDocumentStatus', 3);
                    commit( 'setErrorData', error);
                    console.log(error)
                })
        },

        /*
        *  Create (insert) a document within the collection
        *  This method and 'duplicateDocument' are basically synonymous
        */
        createDocument( { commit }, data ) {
            commit( 'setCreateDocumentStatus', 1);

            CollectionApi.createDocument( data )
                .then( (response) => {
                    if (response.data.message === 'success') {
                        commit( 'setCreatedDocument', response.data.data.document );
                        commit( 'setCreateDocumentStatus', 2)
                    } else {
                        commit( 'setCreateDocumentStatus', 3);
                        let error = response.data.data ? response.data.data : 'An unhandled error occurred';
                        commit( 'setErrorData', error)
                    }
                })
                .catch( (error) => {
                    commit( 'setCreateDocumentStatus', 3);
                    commit( 'setErrorData', error);
                    console.log(error)
                })
        },

        /*
        *   Delete one or more documents - remove document from array
        */
        deleteDocument( { commit }, data ) {
            commit( 'setDeleteDocumentStatus', 1);

            CollectionApi.deleteDocument( data )
                .then( ( response ) => {
                    // ToDo: we are just handling single doc delete today
                    if (response.data.message === 'success') {
                        commit( 'setDeleteDocumentStatus', 2 );
                        commit( 'setDeletedDocument', [data._id] )
                    } else {
                        commit( 'setDeleteDocumentStatus', 3 );
                        commit( 'setErrorData', 'not deleted')
                    }
                })
                .catch( (error) => {
                    commit( 'setDeleteDocumentStatus', 3 );
                    commit( 'setErrorData', error);
                    console.log(error)
                })
        },

        getLocalCollection( { commit, getters, rootGetters }, data) {
            const dbs = getters.getDatabases;
            let database = dbs.filter((db) => db.db && db.db.name && db.db.name === data.database)[0];
            if (database.collections.length > 0) {
                commit( 'setCollection', {
                    collection: {
                        collection:  database.collections[0].collection,
                        objects:  database.collections[0].objects,
                        server: null,
                        stats:  database.collections[0].stats
                    }
                });
                commit( 'setErrorData', rootGetters.getLanguageString('errors', 'collection.unableToReadCollection'));
                commit( 'setCollectionLoadStatus', 2 )
                return true
            }
            commit( 'setCollectionLoadStatus', 3 )
        },

        setDbCollections( { commit }, data) {
            commit( 'setCollections', data)
        },

        /*
        *   Set the active collection - used for collection tracking
        */
        setActiveCollection( { commit }, data ) {
            commit( 'setActiveCollection', data )
        },

        /*
        *   Set the current format for data entry (json | array)
        */
        setCurrentFormat( { commit }, data ) {
            commit( 'setCurrentFormat', data )
        },

        setDocument( { commit }, data ) {
            commit( 'setDocumentUpdates', data)
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
            state.collectionLoadStatus = status
        },

        /*
        *   Sets the collections
        */
        setCollections( state, collections ) {
            state.collections = collections
        },

        /*
        *   Set the collection load status
        */
        setCollectionLoadStatus( state, status ) {
            state.collectionLoadStatus = status
        },

        /*
        *   Sets the collection
        */
        setCollection( state, collection ) {
            state.collection = collection
        },

        /*
        *   Set the collection save properties status
        */
        setCollectionPropertiesStatus( state, status ) {
            state.collectionPropertiesStatus = status
        },

        /*
        *   Set the collection save index status
        */
        setCollectionIndexStatus( state, status ) {
            state.collectionIndexStatus = status
        },

        setCollectionIndex( state, index ) {
            state.collectionIndex = index
        },

        /*
        *   Set the collection save rename status
        */
        setCollectionRenameStatus( state, status ) {
            state.collectionRenameStatus = status
        },

        /*
        *   Set the collection duplicate status
        */
        setCollectionDuplicateStatus( state, status ) {
            state.collectionDuplicateStatus = status
        },

        /*
        *   Set the collection validation status
        */
        setCollectionValidationStatus( state, status ) {
            state.collectionValidationStatus = status
        },

        setCollectionValidation( state, validation ) {
            state.collectionValidation = validation
        },

        /*
        *   Set the display collection
        */
        setDisplayCollection( state, collection) {
            state.displayCollection = collection
        },

        /*
        *   Set the display collection status
        */
        setDisplayCollectionStatus( state, status) {
            state.displayCollectionStatus = status
        },

        /*
        *   Set the create collection status
        */
        setCreateCollectionStatus( state, status) {
            state.createCollectionStatus = status
        },

        /*
        *   Add the new collection into the existing array
        */
        setCreatedCollection( state, collection ) {
            state.collections.push( collection )
        },

        /*
        *   Set the delete collection status
        */
        setDeleteCollectionStatus( state, status) {
            state.deleteCollectionStatus = status
        },

        /*
        *   Set the deleting collection value
        */
        setDeletingCollection( state, collection) {
            state.deletingCollection = collection
        },

        /*
        *   Set (remove) the deleted collection(s) from the existing array
        */
        setDeletedCollection( state, collections ) {
            /*collections.forEach(function(value, index) {
                let arr = [];
                state.collections.forEach(function(db, index) {
                    if (db.db.name !== value) {
                        arr.push(db);
                    }
                });
                state.collections = arr;
            });*/
        },

        /*
        *   Set the clear collection status
        */
        setClearCollectionStatus( state, status) {
            state.clearCollectionStatus = status
        },

        /*
        *   Set (remove) the deleted collection(s) from the existing array
        */
        setClearedCollection( state, status ) {
            state.collection.objects.count = 0;
            state.collection.objects.objects = [];
            state.clearCollectionCount = status.deleted
        },

        /*
        *   Set the active collection
        */
        setActiveCollection(state, collection) {
            state.activeCollection = collection
        },

        /*
        *   Set the query collection load status
        */
        setQueryCollectionLoadStatus(state, status) {
            state.queryCollectionLoadStatus = status
        },

        /*
        *   Set the query collection results data
        */
        setQueryCollection(state, documents) {
            state.queryCollection = documents
        },

        /*
        *   Set the query logs load status
        */
        setQueryLogsLoadStatus(state, status) {
            state.queryLogsLoadStatus = status
        },

        /*
        *   Set the query log load data
        */
        setQueryLogs(state, logs) {
            state.queryLogs = logs
        },

        /*
        *   Set the query explain load status
        */
        setQueryExplainStatus(state, status) {
            state.queryExplainStatus = status
        },

        /*
        *   Set the query explain data
        */
        setQueryExplain(state, explain) {
            state.queryExplain = explain
        },

        /*
         *  Set the document update status
         */
        setUpdateDocumentStatus( state, status ) {
            state.documentUpdateStatus = status
        },

        /*
         *  Replace document with updated version
         */
        setUpdatedDocument( state, data ) {
            state.collection.objects.objects[data.index].raw = JSON.parse(data.document)
        },

        /*
         *  Replace document with updated version
         */
        setDocumentUpdates( state, data ) {
            if (data.text) {
                state.collection.objects.objects[data.index].text = data.text;
                state.collection.objects.objects[data.index].data = data.data
            }
        },

        /*
         *  Set the document duplicate status
         */
        setDuplicateDocumentStatus( state, status ) {
            state.documentDuplicateStatus = status
        },

        /*
         *  Set the document create status
         */
        setCreateDocumentStatus( state, status ) {
            state.documentCreateStatus = status
        },

        /*
         *  Add created document and update count
         */
        setCreatedDocument( state, data ) {
            state.collection.objects.objects.push(data);
            state.collection.objects.count += 1
        },

        /*
        *   Set the delete document status
        */
        setDeleteDocumentStatus( state, status) {
            state.documentDeleteStatus = status
        },

        /*
        *   Set (remove) the deleted document(s) from the existing array
        */
        setDeletedDocument( state, documents ) {
            let objects = state.collection.objects.objects;
            let arr = [];
            if (documents.length >= 1) {
                documents.forEach(function(value) {
                    objects.forEach(function(doc) {
                        if (doc._id !== value) {
                            arr.push(doc)
                        }
                    });
                })
            } else {
                objects.forEach(function(doc) {
                    if (doc._id !== documents) {
                        arr.push(doc)
                    }
                })
            }
            state.collection.objects.objects = arr;
            state.collection.objects.count -= 1
        },

        setExportCollectionStatus( state, status ) {
            state.exportCollectionStatus = status
        },

        setExportData( state, data ) {
            state.exportData = data
        },

        setImportCollectionStatus( state, status ) {
            state.importCollectionStatus = status
        },

        /*
        *   Save the error data for reference
        */
        setErrorData( state, errors ) {
            if (errors.message) {
                state.errorData = errors.message
            } else {
                state.errorData = errors
            }
        },

        /*
        *   Set the current format
        */
        setCurrentFormat(state, format) {
            state.currentFormat = format
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
            return state.collectionsLoadStatus
        },

        /*
        *   Return the collections
        */
        getCollections( state ) {
            return state.collections
        },

        /*
        *   Return the collection load status
        */
        getCollectionLoadStatus( state ) {
            return state.collectionLoadStatus
        },

        /*
        *   Return the collection
        */
        getCollection( state ) {
            return state.collection
        },

        /*
        *   Get the collection save properties status
        */
        getCollectionPropertiesStatus( state ) {
            return state.collectionPropertiesStatus
        },

        /*
        *   Set the collection save index status
        */
        getCollectionIndexStatus( state ) {
            return state.collectionIndexStatus
        },

        getCollectionIndex( state ) {
            return state.collectionIndex
        },

        /*
        *   Get the collection save rename status
        */
        getCollectionRenameStatus( state ) {
            return state.collectionRenameStatus
        },

        /*
        *   Get the collection duplicate status
        */
        getCollectionDuplicateStatus( state ) {
            return state.collectionDuplicateStatus
        },

        /*
        *   Get the collection validation statu
        */
        getCollectionValidationStatus( state ) {
            return state.collectionValidationStatus;
        },

        getCollectionValidation( state ) {
            return state.collectionValidation
        },

        /*
        *   Return the display collection status
        */
        getDisplayCollectionStatus( state ) {
            return state.displayCollectionStatus
        },

        /*
        *   Return the display collection
        */
        getDisplayCollection: (state) => (id) => {
            if (state.collection && state.collection.id !== '') {
                return state.collection
            } else {
                let collection = state.collection.find(collection => collection.id === id);
                if (collection) {
                    state.displayCollectionStatus = id;
                    state.displayCollection = collection;
                    return collection
                }
            }
        },

        /*
        *   Get the create collection status
        */
        getCreateCollectionStatus( state ) {
            return state.createCollectionStatus
        },

        /*
        *   Get the delete collection status
        */
        getDeleteCollectionStatus( state ) {
            return state.deleteCollectionStatus
        },

        /*
        *   Get the deleting collection value
        */
        getDeletingCollection( state) {
            return state.deletingCollection
        },

        /*
        *   Get the clear collection status
        */
        getClearCollectionStatus( state ) {
            return state.clearCollectionStatus
        },

        /*
        *   Get the stats array (object) from the collection object
        */
        getCollectionStats( state ) {
            if (state.collection) {
                return state.collection.stats
            }
        },

        /*
        *   Fetch any active error
        */
        getCollectionErrorData( state ) {
            return state.errorData
        },

        /*
        *   Return the export status
        */
        getExportCollectionStatus( state ) {
            return state.exportCollectionStatus
        },

        /*
        *   Return the export data when no download is required
        */
        getExportData( state ) {
            return state.exportData
        },

        /*
        *   Return the import status
        */
        getImportCollectionStatus( state ) {
            return state.importCollectionStatus
        },

        /*
        *   Get the active collection
        */
        getActiveCollection( state) {
            return state.activeCollection
        },

        /*
        *   Get the query collection load status
        */
        getQueryCollectionLoadStatus(state) {
            return state.queryCollectionLoadStatus
        },

        /*
        *   Get the query collection results data
        */
        getQueryCollection(state) {
            return state.queryCollection
        },

        /*
        *   Get the query logs load status
        */
        getQueryLogsLoadStatus( state) {
            return state.queryLogsLoadStatus
        },

        /*
        *   Get the query logs data
        */
        getQueryLogs( state) {
            return state.queryLogs
        },

        /*
        *   Get the query explain load status
        */
        getQueryExplainStatus(state) {
            return state.queryExplainStatus
        },

        /*
        *   Get the query explain data
        */
        getQueryExplain(state) {
            return state.queryExplain
        },

        /*
        *   Det the current format
        */
        getCurrentFormat( state ) {
            return state.currentFormat
        },

        /*
         *  Get the document update status
         */
        getUpdateDocumentStatus (state ) {
            return state.documentUpdateStatus
        },

        /*
         *  Get the document duplicate status
         */
        getDuplicateDocumentStatus (state ) {
            return state.documentDuplicateStatus
        },

        /*
         *  Get the document create status
         */
        getCreateDocumentStatus (state ) {
            return state.documentCreateStatus
        },

        /*
        *   Get the delete document status
        */
        getDeleteDocumentStatus( state ) {
            return state.documentDeleteStatus
        },

        /*
         *  Get the documents from the current collection
         */
        getDocuments( state ) {
            return state.collection.objects ? state.collection.objects.objects : null
        },

        /*
         *  Get a document from the current collection
         */
        getDocument: ( state ) => ( id ) => {
            let documents = state.collection.objects.objects;
            if (documents) {
                return documents.map( document => {
                    return document._id = id
                });
            }
        },
    }
};
