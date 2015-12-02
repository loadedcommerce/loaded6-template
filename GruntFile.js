/*
	
	GruntFile.js created by Jay for Factor 3.

*/
module.exports = function(grunt) {
  
  	// Project configuration.
	grunt.initConfig({

		// Package
		pkg: grunt.file.readJSON('package.json'),

		// Uglify
		uglify: {
			options: {
				banner: '/*! <%= pkg.name %> <%= grunt.template.today("yyyy-mm-dd") %> */\n'
			},
			build: {
				src: 'src/js/main.js',
				dest: '<%= pkg.theme_name %>/js/main.js'
			}
		},
	  	
	  	/* This configures the SASS files */
		sass: {
			dist: {
				files: {
					'<%= pkg.theme_name %>/css/style.css' : 'src/sass/style.scss',
					'<%= pkg.theme_name %>/css/bootstrap.css' : 'src/sass/bootstrap.scss'
				}
			}
		},

		/* Watch every file */
		watch: {
			css: {
				files: ['src/*', 'src/**'],
				tasks: ['uglify', 'sass', 'copy']
			},
			js: {
				files: ['<%= pkg.theme_name %>/*', '<%= pkg.theme_name %>/**'],
				tasks: ['uglify', 'copy']
			}
		},

		/* Copy over the files */
		copy: {
			main: {
				src: ['<%= pkg.theme_name %>/*', '<%= pkg.theme_name %>/**'],
				dest: '<%= pkg.theme_path %>'
			},
		},

	});

	// Include the plugins
	grunt.loadNpmTasks('grunt-contrib-sass');
	grunt.loadNpmTasks('grunt-contrib-watch');
	grunt.loadNpmTasks('grunt-contrib-uglify');
	grunt.loadNpmTasks('grunt-contrib-copy');

	// Register commands
	grunt.registerTask('default', ['uglify', 'sass', 'copy']);
};