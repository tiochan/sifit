create user 'sifit_user'@'%' identified by 's1f1t@713';
create database sifit;
grant all privileges on sifit.* to sifit_user@'%';
