# Cargofive

Despliegue en el servidor:

1) Antes de comprimir el código, cambiar la configuración de BD en config/database.php y descomentar líneas de RDS

2) Editar el .env y colocar las variables de producción

3) Comprimir código en formato zip, excluyendo los directorios node_modules y vendor

4) Desplegar código en el servidor

5) Finalmente, conectarse vía consola y correr los siguientes comandos:
	
	- rm -r /var/www/html/public/storage
	- cd /var/www/html
  	- php artisan storage:link
  	- sudo chmod 777 /var/www/html/public
	- sudo chmod 777 /var/www/html/storage
	- php artisan config:cache
	- php artisan cache:clear
