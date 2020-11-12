/* jshint node:true */
module.exports = function (grunt) {
    'use strict';

    const sass = require('node-sass');
    require('load-grunt-tasks')(grunt);

    var odinConfig = {

        // gets the package vars
        pkg: grunt.file.readJSON('package.json'),

        // setting folder templates
        dirs: {
            css: '../css',
            js: '../js',
            sass: '../sass',
            images: '../images',
            tmp: 'tmp'
        },

        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '<%= dirs.js %>/.jshintrc'
            },
            all: [
                'Gruntfile.js',
                '<%= dirs.js %>/main.js'
            ]
        },

        // uglify to concat and minify
        uglify: {
            dist: {
                files: {
                    '<%= dirs.js %>/main.min.js': [
                        '<%= dirs.js %>/main.js'    // Custom JavaScript
                    ]
                }
            }
        },

        // compile scss/sass files to CSS
        sass:
        {
            options: {
                implementation: sass,
                sourceMap: true,
                style: 'compressed'
            },
            dist: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.sass %>',
                    src: ['*.scss'],
                    dest: '<%= dirs.css %>',
                    ext: '.css'
                }]
            }
        },

        // watch for changes and trigger sass, jshint, uglify and livereload browser
        watch: {
            sass: {
                files: [
                    '<%= dirs.sass %>/**'
                ],
                tasks: ['sass']
            },
            js: {
                files: [
                    '<%= jshint.all %>'
                ],
                tasks: ['jshint', 'uglify']
            },
            livereload: {
                options: {
                    livereload: true
                },
                files: [
                    '<%= dirs.css %>/*.css',
                    '<%= dirs.js %>/*.js',
                    '../**/*.php'
                ]
            },
            options: {
                spawn: false
            }
        },

        // image optimization
        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true
                },
                files: [{
                    expand: true,
                    filter: 'isFile',
                    cwd: '<%= dirs.images %>/',
                    src: '**/*.{png,jpg,gif}',
                    dest: '<%= dirs.images %>/'
                }]
            }
        },

        // deploy via rsync
        rsync: {
            options: {
                args: ['--verbose'],
                exclude: [
                    '**.DS_Store',
                    '**Thumbs.db',
                    '.editorconfig',
                    '.git/',
                    '.gitignore',
                    '.jshintrc',
                    'sass/',
                    'src/',
                    'README.md',
                    '.ftppass'
                ],
                recursive: true,
                syncDest: true
            },
            staging: {
                options: {
                    src: '../',
                    dest: '~/PATH/wp-content/themes/odin',
                    host: 'user@host.com'
                }
            },
            production: {
                options: {
                    src: '../',
                    dest: '~/PATH/wp-content/themes/odin',
                    host: 'user@host.com'
                }
            }
        },

        // ftp deploy
        // ref: https://npmjs.org/package/grunt-ftp-deploy
        'ftp-deploy': {
            build: {
                auth: {
                    host: 'ftp.SEU-SITE.com',
                    port: 21,
                    authPath: '../.ftppass',
                    authKey: 'key_for_deploy'
                },
                src: '../',
                dest: '/PATH/wp-content/themes/odin',
                exclusions: [
                    '../**.DS_Store',
                    '../**Thumbs.db',
                    '../.git/*',
                    '../*.md',
                    '../.gitignore',
                    '../js/main.js',
                    '../*.zip',
                    '../*.sublime-project',
                    '../*.sublime-workspace',
                    '../src/**',
                    '../.ftppass'
                ]
            }
        },

        // zip the theme
        zip: {
            dist: {
                cwd: '../',
                src: [
                    '../**',
                    '!../src/**',
                    '!../**.md',
                    '!<%= dirs.sass %>/**',
                    '!../**.zip',
                    '<%= dirs.js %>/main.min.js'
                ],
                dest: '../<%= pkg.name %>.zip'
            }
        },

        // downloads dependencies
        curl: {
        },

        // unzip files
        unzip: {
        },

        // renames and moves directories and files
        rename: {
        },

        // clean directories and files
        clean: {
            options: {
                force: true
            }
        },

        replace: {
        }

    };

    // Initialize Grunt Config
    // --------------------------
    grunt.initConfig(odinConfig);

    // Register Tasks
    // --------------------------

    // Default Task
    grunt.registerTask('default', [
        'jshint',
        'sass',
        'uglify'
    ]);

    // Optimize Images Task
    grunt.registerTask('optimize', ['imagemin']);

    // Deploy Tasks
    grunt.registerTask('ftp', ['ftp-deploy']);

    // Compress
    grunt.registerTask('compress', [
        'default',
        'zip'
    ]);

    // Short aliases
    grunt.registerTask('w', ['watch']);
    grunt.registerTask('o', ['optimize']);
    grunt.registerTask('f', ['ftp']);
    grunt.registerTask('r', ['rsync']);
    grunt.registerTask('c', ['compress']);
};
