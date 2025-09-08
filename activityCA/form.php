<!DOCTYPE html>
<html>
<head>
    <title>Form Generator</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #1c1c1c, #3a3a3a); /* black-grey gradient */
            color: #eaeaea;
        }
        header {
            background: #111;
            color: #fff;
            padding: 20px;
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
        }
        .card {
            background: #2a2a2a;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0px 4px 15px rgba(0,0,0,0.6);
            margin-bottom: 20px;
            animation: fadeIn 0.5s ease-in-out;
        }
        h2, h3 {
            color: #fff;
            margin-bottom: 15px;
            border-bottom: 1px solid #444;
            padding-bottom: 5px;
        }
        label {
            font-weight: bold;
            display: block;
            margin: 8px 0 5px;
            color: #ddd;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #555;
            border-radius: 6px;
            font-size: 14px;
            background: #1c1c1c;
            color: #eee;
            transition: 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #999;
            outline: none;
            box-shadow: 0px 0px 5px rgba(255,255,255,0.2);
        }
        input[type=submit] {
            background: #444;
            color: #fff;
            border: none;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        input[type=submit]:hover {
            background: #666;
        }
        .result {
            background: #1e1e1e;
            border-left: 5px solid #999;
            padding: 15px;
            border-radius: 6px;
        }
        .error {
            color: #ff5555;
            font-size: 13px;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <header>Form Generator</header>
    <div class="container">

    <?php
    $type = $_POST['type'] ?? '';
    $submitted = isset($_POST['submit']);

    // Step 1: Form type selector
    echo '<div class="card">';
    echo '<form method="post">';
    echo '<label>Select Form Type:</label>';
    echo '<select name="type" required>';
    $formTypes = ["contact"=>"Contact Form", "survey"=>"Survey Form", "order"=>"Order Form", "register"=>"Registration Form"];
    echo '<option value="">-- Choose Form Type --</option>';
    
    // use loop for form types
    foreach ($formTypes as $key => $label) {
        $selected = ($type==$key) ? "selected" : "";
        echo "<option value='$key' $selected>$label</option>";
    }
    echo '</select>';
    echo '<input type="submit" name="choose" value="Generate Form">';
    echo '</form>';
    echo '</div>';

    // Step 2: Generate form fields
    if ($type != '' && !$submitted) {
        echo "<div class='card'>";
        echo "<form method='post'>";
        echo "<input type='hidden' name='type' value='$type'>";

        switch ($type) {
            case "contact":
                echo "<h3>Contact Form</h3>";
                echo "<label>Name:</label><input type='text' name='name'>";
                echo "<label>Email:</label><input type='text' name='email'>";
                echo "<label>Message:</label><textarea name='message'></textarea>";
                break;

            case "survey":
                echo "<h3>Survey Form</h3>";
                echo "<label>Rate our service:</label>";
                echo "<select name='rating'>";
                $ratings = ["Excellent", "Good", "Average", "Poor"];
                for ($i=0; $i<count($ratings); $i++) {
                    echo "<option>".$ratings[$i]."</option>";
                }
                echo "</select>";
                echo "<label>Feedback:</label><textarea name='feedback'></textarea>";
                break;

            case "order":
                echo "<h3>Order Form</h3>";
                echo "<label>Product:</label><input type='text' name='product'>";
                echo "<label>Quantity:</label><input type='number' name='qty'>";
                echo "<label>Price:</label><input type='text' name='price'>";
                break;

            case "register":
                echo "<h3>Registration Form</h3>";
                echo "<label>Username:</label><input type='text' name='username'>";
                echo "<label>Email:</label><input type='text' name='email'>";
                echo "<label>Password:</label><input type='password' name='password'>";
                break;
        }

        echo "<input type='submit' name='submit' value='Submit'>";
        echo "</form>";
        echo "</div>";
    }

    // Step 3: Show results with validation
    if ($submitted) {
        echo "<div class='card result'>";
        echo "<h3>Form Submission Result:</h3>";

        $errors = [];
        foreach ($_POST as $key => $value) {
            if ($key != "submit" && $key != "choose" && $key != "type") {
                if (empty($value)) {
                    $errors[] = ucfirst($key)." cannot be empty.";
                } else {
                    if ($key=="email" && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[] = "Invalid email format.";
                    }
                    if ($key=="password" && strlen($value)<6) {
                        $errors[] = "Password must be at least 6 characters.";
                    }
                }
            }
        }

        if (!empty($errors)) {
            echo "<h4>⚠ Errors:</h4>";
            foreach ($errors as $err) {
                echo "<p class='error'>$err</p>";
            }
        } else {
            foreach ($_POST as $key => $value) {
                if ($key != "submit" && $key != "choose" && $key != "type") {
                    echo "<b>" . ucfirst($key) . ":</b> " . htmlspecialchars($value) . "<br>";
                }
            }
            echo "<p style='color:#00ff88; font-weight:bold;'>✅ Form submitted successfully!</p>";
        }
        echo "</div>";
    }
    ?>
    </div>
</body>
</html>