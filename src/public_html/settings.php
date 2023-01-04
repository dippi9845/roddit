

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <title>Roddit</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato">
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col">
                <h1 class="text-center">Settings</h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="alert alert-danger text-center" role="alert" style="display: none;"><span></span></div>
            </div>
        </div>
        <div class="row">
            <div class="col-4">
            <img>
            </div>
            <div class="col">
                <form>
                    <label class="form-label">Change email</label>
                    <input class="form-control" type="email" placeholder="New Email" name="new-email">
                    <label class="form-label">Change Nickname</label>
                    <input class="form-control" type="text" placeholder="New nickname" name="new-nickname">
                    <label class="form-label">Change Biography</label>
                    <textarea class="form-control" placeholder="Biography"></textarea>
                    <label class="form-label">Change password</label>
                    <input class="form-control" type="password" name="new-pass" placeholder="New Password">
                    <input class="form-control" type="password" name="confirm-new-pass" placeholder="Confirm new password">
                    <label class="form-label">Change Profile image</label>
                    <input class="form-control" type="file">
                    <button class="btn btn-primary" type="submit">Update</button>
                </form>
            </div>
        </div>
    </div>
    <script src="assets/bootstrap/js/bootstrap.min.js"></script>
</body>

</html>