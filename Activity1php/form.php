<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Custom Dynamic Form Builder</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0; min-height: 100vh;
      background: linear-gradient(135deg, #4facfe, #7366ff);
      display: flex; justify-content: center; align-items: center; padding: 40px 20px;
      color: #111827;
    }
    .wrapper {
      width: 100%; max-width: 1000px;
      background: #ffffff;
      border-radius: 18px;
      padding: 40px;
      box-shadow: 0 12px 35px rgba(0,0,0,0.25);
    }
    h2 {
      text-align: center;
      font-size: 30px;
      font-weight: 700;
      margin-bottom: 25px;
     font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
      
    }
    p { font-size: 15px; margin-bottom: 20px; color: #6b7280; }
    label { display: block; font-weight: 600; margin-bottom: 8px; font-size: 15px; color: #111827; }
    input[type="text"], input[type="number"], input[type="email"], input[type="password"], textarea, select {
      width: 100%; padding: 15px; font-size: 16px;
      border: 1px solid #d1d5db; border-radius: 10px; margin-bottom: 20px;
      background: #f9fafb; transition: 0.3s;
    }
    input:focus, textarea:focus, select:focus {
      border-color: #7366ff;
      box-shadow: 0 0 6px rgba(115,102,255,0.5);
      outline: none;
    }
    textarea { min-height: 130px; }
    button {
      width: 100%; padding: 14px; border: none; border-radius: 12px;
      background: linear-gradient(135deg,#ff6a88,#ffcc70);
      color: #fff; font-size: 18px; font-weight: 700; cursor: pointer;
      transition: 0.3s;
    }
    button:hover { opacity: 0.95; transform: scale(1.02); }
    .errors {
      background: #ffe4e6; border: 1px solid #fecdd3;
      padding: 15px; border-radius: 12px;
      margin-bottom: 20px; color: #e11d48; font-weight: 600;
    }
    .output {
      margin-top: 20px; padding: 20px;
      background: #ecfdf5; border: 1px solid #bbf7d0;
      border-radius: 12px; color: #166534;
    }
    .output h3 { margin-top: 0; font-size: 20px; font-weight: 700; color: #16a34a; }
    .output p { margin-bottom: 12px; font-size: 16px; color: #166534; }
    .builder-row {
      display: grid; grid-template-columns: 1fr 160px;
      gap: 15px; margin-bottom: 20px;
    }
  </style>
</head>
<body>
<div class="wrapper">
  <h2>Custom Form Builder</h2>

  <?php
  // Step 1: Ask how many fields user wants
  if (!isset($_POST['count']) && !isset($_POST['generate']) && !isset($_POST['submitForm'])) {
      echo '<form method="post">';
      echo '<label>How many fields do you want?</label>';
      echo '<input type="number" name="count" min="1" placeholder="Enter number of fields">';
      echo '<button type="submit">Next</button>';
      echo '</form>';
  }

  // Step 2: Show builder rows according to number
  elseif (isset($_POST['count'])) {
      $count = intval($_POST['count']);
      echo "<form method='post'>";
      echo "<p>Define your $count fields (Label + Type).</p>";
      for ($i = 1; $i <= $count; $i++) {
          echo "<div class='builder-row'>
                  <input type='text' name='labels[]' placeholder='Field Label (e.g. Field $i)'>
                  <select name='types[]'>
                    <option value='text'>Text</option>
                    <option value='email'>Email</option>
                    <option value='number'>Number</option>
                    <option value='password'>Password</option>
                    <option value='textarea'>Textarea</option>
                  </select>
                </div>";
      }
      echo "<button type='submit' name='generate'>Generate Form</button>";
      echo "</form>";
  }

  // Step 3: Generate custom form
  elseif (isset($_POST['generate']) && !isset($_POST['submitForm'])) {
      $labels = $_POST['labels'];
      $types = $_POST['types'];
      echo "<form method='post'>";
      for ($i = 0; $i < count($labels); $i++) {
          $lab = trim($labels[$i]);
          if ($lab != "") {
              echo "<input type='hidden' name='labels[]' value='" . htmlspecialchars($lab) . "'>";
              echo "<input type='hidden' name='types[]' value='" . $types[$i] . "'>";
              echo "<label>" . htmlspecialchars($lab) . "</label>";
              switch ($types[$i]) {
                  case "email":
                      echo "<input type='email' name='field_$i'>";
                      break;
                  case "number":
                      echo "<input type='number' name='field_$i'>";
                      break;
                  case "password":
                      echo "<input type='password' name='field_$i'>";
                      break;
                  case "textarea":
                      echo "<textarea name='field_$i'></textarea>";
                      break;
                  default:
                      echo "<input type='text' name='field_$i'>";
              }
          }
      }
      echo "<button type='submit' name='submitForm'>Submit</button>";
      echo "</form>";
  }

  // Step 4: Handle submission and display data
  elseif (isset($_POST['submitForm'])) {
      $labels = $_POST['labels'];
      $types = $_POST['types'];
      $errors = [];
      $values = [];
      for ($i = 0; $i < count($labels); $i++) {
          $lab = trim($labels[$i]);
          $val = trim($_POST["field_$i"]);
          $values[$i] = htmlspecialchars($val);
          if ($val === "") $errors[] = "$lab cannot be empty.";
          else if ($types[$i] == "email" && !filter_var($val, FILTER_VALIDATE_EMAIL)) $errors[] = "$lab must be a valid email.";
          else if ($types[$i] == "number" && !is_numeric($val)) $errors[] = "$lab must be a number.";
      }
      if (count($errors) > 0) {
          echo "<div class='errors'><ul>";
          foreach ($errors as $e) echo "<li>$e</li>";
          echo "</ul></div>";
      } else {
          echo "<div class='output'><h3>Submitted Data</h3>";
          for ($i = 0; $i < count($labels); $i++) {
              echo "<p><b>" . htmlspecialchars($labels[$i]) . ":</b> " . $values[$i] . "</p>";
          }
          echo "</div>";
      }
  }
  ?>
</div>
</body>
</html>
