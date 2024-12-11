<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hospital Token Management Panel</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #ffffff; /* Main color: White */
      color: #333333; /* Darker text for readability */
    }

    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 20px;
    }

    .header {
      text-align: center;
      margin-bottom: 30px;
    }

    .chart-section {
      margin-top: 30px;
    }

    canvas {
      max-width: 100%;
    }

    /* Table styling */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 30px;
    }

    th, td {
      padding: 12px;
      text-align: center;
      border: 1px solid #ddd;
    }

    th {
      background-color: #f7d0d4; /* Dusty pink for table headers */
      color: #333; /* Dark text for contrast */
    }

    td {
      background-color: #fafafa; /* Light background for table data */
    }

    tr:hover {
      background-color: #fbe7e9; /* Light dusty pink on row hover */
    }

    /* Patient details table */
    .patient-details-table {
      width: 100%;
      margin: 20px 0;
      border-collapse: collapse;
    }

    .patient-details-table th, .patient-details-table td {
      text-align: left;
      padding: 12px;
    }

    .patient-details-table th {
      background-color: #f7d0d4; /* Dusty pink for table header */
      color: #333; /* Dark text for readability */
    }

    .patient-details-table td {
      background-color: #fafafa; /* Light gray for the content */
    }

    .patient-details-table tr:hover {
      background-color: #fbe7e9; /* Light pink hover effect */
    }

    /* Additional style to highlight specific table rows or columns if needed */
    .highlight {
      font-weight: bold;
      color: #e03e72; /* Darker dusty pink for emphasis */
    }
  </style>
</head>
<body>
  <div class="container">
    <header>
      <h1>Hospital Token Management Panel</h1>
    </header>

    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Database credentials
    $servername = "sql112.infinityfree.com";
    $username = "if0_37232855";
    $password = "Shivansh123451";
    $dbname = "if0_37232855_medidata";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Query for all patients' details
    $patientQuery = "SELECT patientName, age, token FROM appointment";
    $patientResult = $conn->query($patientQuery);

    $patients = [];
    if ($patientResult->num_rows > 0) {
        while ($patient = $patientResult->fetch_assoc()) {
            $patients[] = $patient;  // Store patient data in an array
        }
    } else {
        $patients[] = ['patientName' => 'Unknown', 'age' => 'N/A', 'token' => 'N/A'];
    }

    // Query for token generation data in the past 7 days
    $historyQuery = "SELECT DATE(created_at) as date, COUNT(*) as count FROM appointment WHERE created_at >= CURDATE() - INTERVAL 7 DAY GROUP BY DATE(created_at)";
    $historyResult = $conn->query($historyQuery);

    $dates = [];
    $tokensCount = [];
    while ($row = $historyResult->fetch_assoc()) {
        $dates[] = $row['date'];
        $tokensCount[] = $row['count'];
    }

    // Close the connection
    $conn->close();
    ?>

    <!-- Patient Details Table Section -->
    <section class="patient-details-section">
      <h2>Patient Details</h2>
      <table class="patient-details-table">
        <thead>
          <tr>
            <th>Field</th>
            <th>Details</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Loop through all the patients and display their details
            foreach ($patients as $patient) {
                echo "<tr><td>Name</td><td>" . $patient['patientName'] . "</td></tr>";
                echo "<tr><td>Age</td><td>" . $patient['age'] . "</td></tr>";
                echo "<tr><td>Token</td><td>" . $patient['token'] . "</td></tr>";
                echo "<tr><td colspan='2'><hr></td></tr>";
            }
          ?>
        </tbody>
      </table>
    </section>

    <!-- Table Section for Token Data -->
    <section class="table-section">
      <h2>Token Generation Data</h2>
      <table>
        <thead>
          <tr>
            <th>Date</th>
            <th>Tokens Generated</th>
          </tr>
        </thead>
        <tbody>
          <?php
            // Loop through the data and display it in the table
            for ($i = 0; $i < count($dates); $i++) {
                echo "<tr>";
                echo "<td>" . $dates[$i] . "</td>";
                echo "<td>" . $tokensCount[$i] . "</td>";
                echo "</tr>";
            }
          ?>
        </tbody>
      </table>
    </section>

    <!-- Chart Section -->
    <section class="chart-section">
      <h2>Token Generation Statistics</h2>
      <canvas id="tokenChart" width="400" height="200"></canvas>
      <script>
        console.log("Dates from PHP:", <?php echo json_encode($dates); ?>);
        console.log("Token Counts from PHP:", <?php echo json_encode($tokensCount); ?>);

        var ctx = document.getElementById('tokenChart').getContext('2d');
        var tokenChart = new Chart(ctx, {
          type: 'line',
          data: {
            labels: <?php echo json_encode($dates); ?>, // Dates for past week
            datasets: [{
              label: 'Tokens Generated (Past Week)',
              data: <?php echo json_encode($tokensCount); ?>, // Token counts for the past week
              backgroundColor: 'rgba(223, 113, 122, 0.2)', // Dusty pink background color
              borderColor: 'rgba(223, 113, 122, 1)', // Dusty pink border color
              borderWidth: 2,
              fill: true
            }]
          },
          options: {
            responsive: true,
            scales: {
              x: {
                title: {
                  display: true,
                  text: 'Date'
                }
              },
              y: {
                title: {
                  display: true,
                  text: 'Tokens Generated'
                },
                beginAtZero: true
              }
            },
            plugins: {
              legend: {
                labels: {
                  font: {
                    size: 14
                  }
                }
              }
            }
          }
        });
      </script>
    </section>
  </div>
</body>
</html>
