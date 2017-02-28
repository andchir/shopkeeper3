module.exports = function(grunt) {
    
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        uglify: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                sourceMap: true
            },
            javascript: {
                files: {
                    'dist/js/scripts_all.min.js': [
                        'js/jquery-3.1.1.js',
                        'bootstrap/js/bootstrap.js',
                        'js/viewerjs/dist/viewer.js',
                        'js/slick/slick.js',

                        //Shopkeeper
                        '../components/shopkeeper3/web/js/lang/ru.js',
                        '../components/shopkeeper3/web/js/shopkeeper.js',

                        //TagManager
                        '../components/tag_manager2/js/web/jquery-ui-1.10.3.custom.min.js',
                        '../components/tag_manager2/js/web/jquery.history.src.js',
                        '../components/tag_manager2/js/web/filters.js',
                        '../components/tag_manager2/js/web/view_switch.js',

                        //js for template
                        'js/script.js'
                    ]
                }
            }
        },
        cssmin: {
            options: {
                banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n',
                sourceMap: true
            },
            target: {
                files: {
                    'dist/css/styles_all.min.css': [
                        'bootstrap/css/bootstrap.css',
                        'bootflat/bootflat/css/bootflat.css',
                        'js/slick/css/slick.css',
                        'js/slick/css/slick-theme.css',
                        'js/viewerjs/dist/viewer.css',
                        'angularicons/src/css/angularicons.css',
                        '../components/tag_manager2/css/web/tm-style.css',
                        'css/custom.css'
                    ]
                }
            }
        },
        watch: {
            javascript: {
                files: ['js/script.js'],
                tasks: ['uglify']
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    
    grunt.registerTask('default', ['uglify','cssmin']);
    
};
