<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Projeto

- Atualmente o projeto utiliza a porta padrão 8084, você pode modifica-la em .env
- Atualize as tabelas utilizando o comando migrate --seed, para criar também a seeder de operações
- Banco simplificado utilizando o sqlite. Caso deseje alterar, exclusa o arquivo e altere a configuração em `database.php

## API EXEMPLOS

```
POST

/api/transactions

{
    "card": "4753****3153",
    "cpf": "09620676017",
    "date_at": "20190301",
    "hour_at": "153453",
    "name": "JOÃO MACEDO",
    "store_name": "BAR DO JOÃO",
    "type": 1,
    "value": 300
}   

OBS: Realiza a criação do cliente, caso o cpf nõ seja encontrado.


GET

/api/transactions

[
    {
        "id": 1,
        "type": "3",
        "value": "142",
        "date_at": "2019-03-01 00:00:00",
        "hour_at": "15:34:53",
        "clients": {
            "id": 1,
            "cpf": "09620676017",
            "card": "4753****3153",
            "amount": "182.0",
            "users": {
                "id": 1,
                "name": "JoÃo macedo"
            }
        },
        "stores": {
            "id": 1,
            "name": "JoÃo macedo"
        },
        "operations": {
            "id": 3,
            "code_operation": "3",
            "description": "Financiamento",
            "type_description": "Saída",
            "type": "0",
            "created_at": "2023-02-04 23:04:30",
            "updated_at": "2023-02-04 23:04:30"
        }
    },
]
```

```
 GET 
 
 /api/operations
 
 [
 	{
		"id": 1,
		"code_operation": "1",
		"description": "Débito",
		"type_description": "Entrada",
		"type": "1",
		"created_at": "2023-02-05 19:46:53",
		"updated_at": "2023-02-05 19:46:53"
	},
	{
		"id": 2,
		"code_operation": "2",
		"description": "Boleto",
		"type_description": "Saída",
		"type": "0",
		"created_at": "2023-02-05 19:46:53",
		"updated_at": "2023-02-05 19:46:53"
	},
 ]
```

```
 GET 
 
 /api/clients
 
 [
    {
    "id": 1,
    "cpf": "09620676017",
    "card": "4753****3153",
    "amount": "300.0",
    "users": {
        "id": 1,
        "name": "JoÃo macedo"
    },
    "stores": {
        "id": 1,
        "name": "JoÃo macedo"
    },
    "transactions": [
        {
            "id": 1,
            "type": "3",
            "value": "300",
            "amount": "300.0",
            "date_at": "2019-03-01 00:00:00",
            "hour_at": "15:34:53",
            "operations": {
                "id": 3,
                "code_operation": "3",
                "description": "Financiamento",
                "type_description": "Saída",
                "type": "0",
                "created_at": "2023-02-05 19:34:21",
                "updated_at": "2023-02-05 19:34:21"
            }
        }
    ]
 ]
```

