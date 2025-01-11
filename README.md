The app designed by my one of client, thus this is the costumized app hosted in Hostinger, the language and other design was asked by the user, the compnay asked single tenant app. The login system is as per below

                             ┌───────────────┐
                             │   ayarlar.php  │
                             └───────────────┘
                                      │
                       ┌─────────────────────────────────┐
                       │ 1) User requests the page       │
                       └─────────────────────────────────┘
                                      │
                                      ▼
              ┌─────────────────────────────────────────────────┐
              │ Check if user is already logged in and is admin│
              │   - session variables: $_SESSION['username'],   │
              │     $_SESSION['role'] == 'admin'?              │
              └─────────────────────────────────────────────────┘
                     │                          │
                     │                          │
                     │                          │
                ( NOT LOGGED IN )         ( LOGGED IN AS ADMIN )
                     │                          │
                     ▼                          ▼
     ┌─────────────────────────────┐   ┌───────────────────────────────────┐
     │   Show login form           │   │   Show Ayarlar (admin-only area) │
     │   - Takes username & pwd    │   │   - Display admin users          │
     │   - Submits via POST        │   │   - Change password form         │
     │   - Action="" (the same     │   │   - Delete users (admin action)  │
     │     ayarlar.php file)       │   └───────────────────────────────────┘
     └─────────────────────────────┘
                     │
                     ▼
        ┌─────────────────────────────────────────────────────┐
        │ 2) Login form is submitted (POST to ayarlar.php)    │
        └─────────────────────────────────────────────────────┘
                     │
                     ▼
       ┌────────────────────────────────────────────────────────────────┐
       │ Query `login` table by `username`                             │
       │  - If user found, compare $password with $user['pwd']         │
       │  - If match and $user['role'] == 'admin':                     │
       │       => $_SESSION['username'] = $user['username']            │
       │       => $_SESSION['role'] = $user['role']                    │
       │       => redirect or display admin page                       │
       │  - Else if password mismatch or non-admin role:               │
       │       => $error = "Invalid credentials or not admin"          │
       └────────────────────────────────────────────────────────────────┘
                     │
                     ▼
       ┌────────────────────────────────────────────────────────────────┐
       │ 3) If login success and role == admin:                        │
       │       => user re-enters ayarlar.php or is redirected there,   │
       │          now session has admin role => Ayarlar content shown. │
       │                                                                │
       │    If failure or not admin:                                   │
       │       => remain on login form with $error                     │
       └────────────────────────────────────────────────────────────────┘




