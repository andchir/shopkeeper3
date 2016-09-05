module.exports = function(grunt) {

  grunt.initConfig({
    jshint: {
      files: ['Gruntfile.js', 'js/**/*.js'],
      options: {
        jshintrc: '.jshintrc'
      }
    },
    cssmin: {
      dist: {
        files: {
          'css/icon.min.css': [
            'css/bootstrap.min.css',
            'css/bootflat.css',
            'css/site-icons.css'

          ],
          'src/css/angularicons.min.css': 'src/css/angularicons.css'
        }
      }
    },
    sass: {
      dist: {
        files: {
          'src/css/angularicons.css': 'src/scss/angularicons.scss'
        },
        options: {                      
          style: 'expanded',
          sourcemap: 'true'
        }
      }
    },
    pkg: grunt.file.readJSON('package.json')
  });

  require('load-grunt-tasks')(grunt);

  grunt.registerTask('default', [
    'jshint',
    'sass',
    'cssmin'
  ]);
};