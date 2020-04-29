pipeline {
    agent any

    environment {
        repositoryUrl = 'https://github.com/alexdu98/Ambiguss.git'
        branch = 'master'
        gitCredentials = '43c044bf-8fe6-4d27-b56b-8864d6e38751'
        deployDir = '/var/deploy/'
        wwwDir = '/var/www/'
        configFile = '/app/config/parameters.yml'
        SYMFONY_ENV = 'prod'
    }

    parameters {
        choice(name: 'ENV', choices: ['preprod', 'ambiguss'], description: '')
    }

    stages {

        stage('Cloning') {
            steps {
                echo 'Cloning...'
                git url: repositoryUrl, credentialsId: gitCredentials, branch: branch
                script {
                    env.COMMIT = sh(script: "git describe --always", returnStdout: true).trim()
                }
                echo 'Cloning ended'
            }
        }

        stage('Build') {
            steps {
                echo 'Building...'
                sh 'composer self-update'
                sh 'composer install --no-progress --prefer-dist --no-scripts'
                sh "cp ${wwwDir}${params.ENV}${configFile} ${WORKSPACE}${configFile}"
                sh 'composer run-script symfony-scripts'
                echo 'Building ended'
            }
        }

        stage('Test') {
            steps {
                echo 'Testing...'
                sh 'vendor/bin/simple-phpunit --log-junit tests_report.xml tests'
                echo 'Testing ended'
            }
        }

        stage('Deploy') {
            steps {
                echo 'Deploying...'
                sh "chown -R :www-data ${WORKSPACE}"
                sh "chmod -R g+w ${WORKSPACE}"
                sh "rsync -a --exclude '.git' ${WORKSPACE}/ ${deployDir}${params.ENV}/${env.COMMIT}"
                sh "unlink ${wwwDir}${params.ENV}"
                sh "ln -s ${deployDir}${params.ENV}/${env.COMMIT} ${wwwDir}${params.ENV}"
                echo 'Deploying ended'
            }
        }
    }

    post {
        always {
            junit 'tests_report.xml'
        }
    }
}
