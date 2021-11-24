### PmpMongoAdmin Setup Scripts

### Files
- pmasetup.sh  (shell script - handles basic application installation)
- SETUP.md     (this README file)
- apache/      (directory containing web server configurations)
  - global/    (global: server wide nested apache configurations)
    - phpMongoAdmin.conf      (configured for localhost and private directory access)
    - phpMongoAdminPublic.php (configured for public access - requires security enhancements)