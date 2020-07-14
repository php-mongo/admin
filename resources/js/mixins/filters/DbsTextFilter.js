export const DbsTextFilter = {
  methods: {
    processDbsTextFilter( db, text ) {
      /*
      *  Only process if the text is greater than 1
      */
      if ( text.length > 1 ) {
        /*
        *  If the db name matches the entered text return true otherwise return false.
        */
        if( db.name.toLowerCase().match( '[^,]*' + text.toLowerCase() + '[,$]*' )) {
            // db is matched
          return true;

        } else {
            // db is not matched
          return false;
        }

      } else {
          // error!! search text not founds
        return true;
      }
    }
  }
};
