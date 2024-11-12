# Projet ToDoList - Tests Unitaires

Ce projet implémente une ToDoList en PHP avec des tests unitaires.

## Prérequis

Avant de commencer, assurez-vous que vous avez les outils suivants installés :

- **Docker** pour un environnement de test

## Installation des dépendances

1. Clonez ce dépôt sur votre machine locale.
2. Ouvrez un terminal et naviguez dans le répertoire du projet.
3. Build le conteneur Docker avec la commande suivante :

```bash
docker build --tag php-testUnit .
```

4. Lancez un conteneur Docker avec la commande suivante :

```bash
docker run --rm -it --volume $PWD:/var/www/html php-testUnit sh
```

5. Exécutez la commande suivante pour installer toutes les dépendances PHP via Composer :

```bash
composer install
```

6. Vous pouvez exécuter les tests en utilisant PHPUnit comme suit :

```bash
./vendor/bin/phpunit
```
