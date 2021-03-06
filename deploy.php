<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'Sellbroke');

// Project repository
set('repository', 'https://github.com/andreysuha2/sellbrocke-app.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', [ ".env" ]);
add('shared_dirs', [ "storage" ]);

// Writable dirs by web server 
add('writable_dirs', [ "storage", "bootstrap" ]);


// Hosts

host('development')
    ->hostname("test19.f5-cloud.top")
    ->user("test19")
    ->port("2285")
    ->stage("development")
    ->set("branch", "development")
    ->identityFile("~/.ssh/id_rsa")
    ->forwardAgent(true)
    ->multiplexing(true)
    ->addSshOption('UserKnownHostsFile', '~/.ssh/known_hosts')
    ->addSshOption('StrictHostKeyChecking', 'no')
    ->set("composer_options", "install --optimize-autoloader --no-dev")
    ->set('deploy_path', '/var/www/test19/data/www/test19.f5-cloud.top');
    
// Tasks

task('deploy:storage-link', function () {
    run('php /var/www/test19/data/www/test19.f5-cloud.top/current/artisan storage:link');
});

desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:symlink',
    'deploy:storage-link',
    'deploy:unlock',
    'cleanup',
    'success'
]);

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.
before('deploy:symlink', 'artisan:migrate');
