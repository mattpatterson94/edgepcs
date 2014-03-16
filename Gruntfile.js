module.exports = function(grunt) {
  grunt.initConfig({
    pkg: grunt.file.readJSON('package.json'),

    sass: {
      options: {
        includePaths: ['bower_components/foundation/scss']
      },
      dist: {
        options: {
          outputStyle: 'compressed'
        },
        files: {
          'css/app.css': 'scss/app.scss'
        }
      }
    },

    concat: {
      dist: {
          src: [
              'bower_components/jquery/dist/jquery.min.js',
              'bower_components/foundation/js/foundation.min.js',
              // 'js/*.js'
              // 'js/!(app.concat|app.min).js'
              'js/parsley.min.js',
              'js/app.js'
          ],
          dest: 'build/js/app.concat.js',
      }
    },

    uglify: {
      options: {
        mangle: false
      },
      build: {
          src: 'build/js/app.concat.js',
          dest: 'build/js/app.min.js'
      }
    },

    imagemin: {
        dynamic: {
            files: [{
                expand: true,
                cwd: 'images/',
                src: ['**/*.{png,jpg,gif}'],
                dest: 'build/images/'
            }]
        }
    },

    watch: {
      livereload: {
          files: ['*.html', '*.php', 'js/**/*.{js,json}', 'css/*.css','img/**/*.{png,jpg,jpeg,gif,webp,svg}'],
          options: {
              livereload: true
          }
      },   

      scripts: {
          files: ['js/*.js'],
          tasks: ['concat', 'uglify'],
          options: {
              spawn: false
          },
      },

      grunt: { files: ['Gruntfile.js'] },

      sass: {
        files: 'scss/**/*.scss',
        tasks: ['sass']
      }
    }
  });

  grunt.loadNpmTasks('grunt-sass');
  grunt.loadNpmTasks('grunt-contrib-watch');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-imagemin');

  grunt.registerTask('build', ['sass']);
  grunt.registerTask('default', ['build','concat','uglify','imagemin','watch']);

}