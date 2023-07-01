<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>DrinkCoff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
            crossorigin="anonymous"></script>
    <link rel="stylesheet" href="/assets/css/index.css">
</head>
<body>

<div class="container">

    <div class="row justify-content-center">
        <div class="col-md-8" id="login-container">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Login</h4>
                        </div>
                        <div class="card-body">
                            <form id="login-form" onsubmit="event.preventDefault(); submitFormLogin();">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="login-email"
                                           aria-describedby="emailHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="login-password">
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4>Register</h4>
                        </div>
                        <div class="card-body">
                            <form id="register-form" onsubmit="event.preventDefault(); submitFormRegister();">
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" id="login-name"
                                           aria-describedby="nameHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputEmail1" class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" id="login-email"
                                           aria-describedby="emailHelp">
                                </div>
                                <div class="mb-3">
                                    <label for="exampleInputPassword1" class="form-label">Password</label>
                                    <input type="password" class="form-control" name="password" id="login-password">
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="col-4">
            <div class="d-none" id="edit-user-container">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4>Edit user</h4>
                        <button onclick="deleteMyUser()" class="btn btn-danger">DELETE</button>
                    </div>
                    <div class="card-body">
                        <form id="edit-form" onsubmit="event.preventDefault(); submitFormEditUser();">
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Name</label>
                                <input type="text" class="form-control" name="name" id="edit-name"
                                       aria-describedby="nameHelp">
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputEmail1" class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" id="edit-email"
                                       aria-describedby="emailHelp">
                            </div>
                            <div class="mb-3">
                                <label for="exampleInputPassword1" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" id="edit-password">
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-none" id="input-output-container">
        <div class="mt-5 row justify-content-center">

            <div class="col-md-5">

                <div class="card">
                    <div class="row justify-content-center">
                        <div class="col-12">

                            <div class="card-header">
                                <h4>Input area</h4>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>My history</h4>
                                        <button onclick="getDrinkHistoric()" class="btn btn-success">GET</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Drink Coffe</h4>
                                        <input class="form-control" type="number" step="1" name="drinks" id="drinks"
                                               value="1"
                                               min="1">
                                        <button onclick="drinkCoffe(document.getElementById('drinks').value)"
                                                class="btn btn-success">POST
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Get list of users</h4>
                                        <button onclick="getListOfUsers()" class="btn btn-success">GET</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Get user info</h4>
                                        <button onclick="getMyUser()" class="btn btn-success">GET</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Get ranking from interval</h4>
                                        <input class="form-control" type="number" step="1" name="interval" id="interval"
                                               value="1" min="1">
                                        <button onclick="getRankingInterval(document.getElementById('interval').value)"
                                                class="btn btn-success">GET
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card-body">
                                <div class="card">
                                    <div class="card-header">
                                        <h4>Get ranking from date</h4>
                                        <input class="form-control" type="date" name="target-date" id="target-date">
                                        <button onclick="getRankingDate(document.getElementById('target-date').value)"
                                                class="btn btn-success">GET
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <h4>Output area</h4>
                    </div>
                    <div class="card-body bg-dark text-white ">
                        <pre id="output"></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="/assets/js/index.js"></script>
</body>
</html>