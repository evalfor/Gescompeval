Installation of Gescompeval:
----------------------------

1. git clone https://github.com/evalfor/Gescompeval.git
2. chmod -R 777 Gescompeval/app/logs
3. chmod -R 777 Gescompeval/app/cache
4. Create an empty data base
5. Modify parameters in Gescompeval/app/config/parameters.yml
6. php Gescompeval/app/console doctrine:schema:create
7. Insert default users by Gescompeval1/users_orm.sql (admin/admin and user/user)
8. php Gescompeval/app/console cache:clear -env:prod
9. rm -r Gescompeval/app/cache/prod/*

Note: 9º point will be necessary everytime you change the code or update to a new version

