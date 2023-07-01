let token = '';
let userId = '';
let loginContainer = document.getElementById('login-container');
let output = document.getElementById('output');

function submitFormLogin() {
    let validated = validateForm('login-form', [
        'email', 'password'
    ]);
    postData("/login",
        {
            'email': validated['email'],
            'password': validated['password'],
        },
        'Logged In', function (data) {
            token = data.data.user.token;
            userId = data.data.user.id;
            document.getElementById('edit-name').value = data.data.user.name
            document.getElementById('edit-email').value = data.data.user.email
            loginContainer.className = loginContainer.className + ' d-none';
            document.getElementById('edit-user-container').className = '';
            document.getElementById('input-output-container').className = '';
        });
}

function submitFormRegister() {
    let validated = validateForm('register-form', [
        'email', 'password', 'name'
    ]);
    postData("/users",
        {
            'name': validated['name'],
            'email': validated['email'],
            'password': validated['password'],
        },
        'Registered, do login',)

}

function submitFormEditUser() {
    let validated = validateForm('edit-form', [
        'email', 'name'
    ]);
    postData("/users/" + userId,
        {
            'name': validated['name'],
            'email': validated['email'],
            'password': document.getElementById('edit-password').value,
        },
        'User Edited', function (data) {
            setOutput(data)
        }, true, 'PUT')

}

function deleteMyUser() {
    postData(`/users/${userId}`,
        {}, 'User Deleted', function (data) {
            setOutput(data)
        }, true, 'DELETE')
}

function drinkCoffe(drinks) {
    postData(`/users/${userId}/drink`,
        {
            'drink': drinks,
        },
        'Drink registered', function (data) {
            setOutput(data)
        }, true)

}


function getListOfUsers() {
    if (token === '') {
        alert("Do login first")
        return;
    }
    getBase('/users')
}

function getMyUser() {
    if (token === '') {
        alert("Do login first")
        return;
    }
    getBase("/users/" + userId);
}


function getRankingInterval(interval = 1) {
    if (token === '') {
        alert("Do login first")
        return;
    }
    getBase('/users/ranking/' + interval);
}

function getRankingDate(date) {
    if (token === '') {
        alert("Do login first")
        return;
    }

    date = date.split('-')

    getBase(`/users/ranking/${date[0]}/${date[1]}/${date[2]}`);
}

function getDrinkHistoric() {
    if (token === '') {
        alert("Do login first")
        return;
    }

    getBase(`/users/${userId}/history`);
}

function postData(route, data = {}, message = '', callback = function (data) {
}, auth = true, method = 'POST') {
    fetch(route, {
        method: method,
        headers: {
            "Content-Type": "application/json",
            "Authorization": auth ? ('Bearer ' + token) : '',
        },
        body: JSON.stringify(data)
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== 200) {
                if (data.data.errors) {
                    alert(JSON.stringify(data.data.errors))
                } else {
                    alert('Error: ' + data.message)
                }
                return;
            }
            console.log("Success:", data);
            callback(data)
            alert(message)
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

function getBase(route) {
    fetch(route, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "Authorization": 'Bearer ' + token
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.status !== 200) {
                alert('Error: ' + data.message)
                return;
            }
            console.log("Success:", data);
            setOutput(data)
        })
        .catch((error) => {
            console.error("Error:", error);
        });
}

function setOutput(data) {
    output.textContent = JSON.stringify(data, undefined, 2);
}

function validateForm(formId, fields = []) {
    let validated = []
    fields.forEach(function (field) {
        let x = document.forms[formId][field].value;
        if (x == "") {
            alert(field + " must be filled out");
            return false;
        }
        validated[field] = x
    })
    return validated
}