<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="./css/output.css" rel="stylesheet">
        <title>TASK Modules - Admin Home</title>
    </head>
    <body style="background-color: gray;">
        <p class="text-4xl">TASK Modules - Admin Home (Rough Draft)</p>

        <div class="grid grid-cols-4 gap-4">
            <div>Hello 1</div>
            <div>Hello 2</div>
            <div>Hello 3</div>
            <div>Hello 4</div>
            <div>Hello 5</div>
            <div>Hello 6</div>
            <div>Hello 7</div>
            <div>Hello 8</div>
            <div>Hello 9</div>
        </div>

        <!-- Temporary form entry for database, will reuse for other components -->
        <fieldset>
            <legend>Form Entry Demo for SQL</legend>
            <form id="frmContact" method="post"
                action="./php/process_demo.php"
                enctype="multipart/form-data"
                style="border-width: 5px; border-style: solid;">
                <p>
                    <label for="firstName">First Name</label>
                    <input type="text" name="firstName" id="firstName">
                </p>
                <p>
                    <label for="lastName">Last Name</label>
                    <input type="text" name="lastName" id="lastName">
                </p>
                <p>
                    <label for="sampleValue">Sample Value</label>
                    <input type="text" name="sampleValue" id="sampleValue">
                </p>
                <p>
                    <label for="image">Upload Image File</label>
                    <input type="file" name="image" id="image">
                </p>
                <p>
                    <input type="submit" name="submit" id="submit" value="Submit">
                </p>
            </form>
        </fieldset>

    </body>
</html>