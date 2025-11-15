<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reports | Mesra Vet Clinic</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
     @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap');
    body {
      font-family: "Poppins", sans-serif;
      background-color: #f5f8ff;
      margin: 0;
      padding: 0;
      color: #333;
    }

    /* NAVBAR */
    .navbar {
      background: #0056b3;
      color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 40px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .navbar .logo {
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .navbar .links a {
      background-color: white;
      color: #0056b3;
      padding: 8px 16px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .navbar .links a:hover {
      background-color: #e6efff;
    }

    .logo-img {
      width: 250px;
      height: 55px;
      margin-right: 15px;
      object-fit: cover;
    }

    /* DASHBOARD CONTAINER */
    .report-container {
      max-width: 1200px;
      margin: 60px auto;
      background: white;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
      padding: 30px 40px;
      animation: fadeIn 0.5s ease-in-out;
    }

    .report-container h2 {
      color: #003d80;
      font-size: 26px;
      margin-bottom: 10px;
    }

    /* FILTER DROPDOWNS */
    .filters {
      display: flex;
      justify-content: center;
      align-items: center;
      flex-wrap: wrap;
      gap: 25px;
      margin-bottom: 30px;
    }

    .dropdown-group {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
    }

    .dropdown-group label {
      font-weight: 600;
      color: #003d80;
      font-size: 14px;
      margin-bottom: 6px;
    }

    .dropdown-group select {
      padding: 10px 16px;
      border-radius: 12px;
      border: 2px solid #0096c7;
      background: #f5f8ff;
      color: #003d80;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 2px 6px rgba(0, 150, 199, 0.15);
    }

    .dropdown-group select:hover {
      border-color: #0077b6;
      box-shadow: 0 3px 10px rgba(0, 119, 182, 0.25);
    }

    .dropdown-group select:focus {
      outline: none;
      border-color: #0056b3;
      box-shadow: 0 0 0 3px rgba(0, 86, 179, 0.2);
    }

    /* SIDE BY SIDE LAYOUT */
    .report-content {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 30px;
      margin-top: 20px;
    }

    /* LEFT: Chart */
    .chart-section {
      flex: 1;
      min-width: 45%;
      background: #f8fbff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
    }

    /* RIGHT: Details */
    #details-section {
      flex: 1;
      min-width: 45%;
      background: #ffffff;
      border-radius: 12px;
      padding: 20px;
      box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
      animation: fadeIn 0.5s ease-in-out;
      display: none;
      max-height: 400px;
      overflow-y: auto;
    }

    canvas {
      width: 100% !important;
      height: 350px !important;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
    }

    th, td {
      padding: 10px 12px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }

    th {
      background-color: #0056b3;
      color: white;
    }

    tr:hover {
      background-color: #eaf7ff;
    }

    @media (max-width: 900px) {
      .report-content {
        flex-direction: column;
      }
      .chart-section, #details-section {
        min-width: 100%;
      }
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">
      <img src="Clinic logo.png" alt="Mesra Vet Clinic Logo" class="logo-img">
    </div>
    <div class="links">
      <a href="staff_dashboard3.php"><i class="fa-solid fa-arrow-left"></i> Back</a>
      
    </div>
  </div>

  <!-- Report Section -->
  <div class="report-container">
    <h2><i class="fa-solid fa-chart-column"></i> Booking Reports</h2>

    <!-- Filters -->
    <div class="filters">
      <div class="dropdown-group">
        <label for="dateRange">Date Range:</label>
        <select id="dateRange">
          <option value="30">Last 30 Days</option>
          <option value="60">Last 60 Days</option>
          <option value="90">Last 90 Days</option>
        </select>
      </div>

      <div class="dropdown-group">
        <label for="viewType">View Type:</label>
        <select id="viewType">
          <option value="weekly">Weekly</option>
          <option value="monthly">Monthly</option>
        </select>
      </div>
    </div>

    <!-- SIDE BY SIDE CONTENT -->
    <div class="report-content">
      <!-- LEFT SIDE: Chart -->
      <div class="chart-section">
        <canvas id="bookingChart"></canvas>
      </div>

      <!-- RIGHT SIDE: Booking Details -->
      <div id="details-section">
        <h3>📅 Booking Details</h3>
        <table id="details-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Pet Owner</th>
              <th>Purpose</th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
  let currentRange = 30;
  let currentView = "weekly";
  const ctx = document.getElementById("bookingChart").getContext("2d");
  let chart;

  // Load chart data
  function loadChart() {
    fetch(`fetch_report_data.php?range=${currentRange}&view=${currentView}`)
      .then(res => res.json())
      .then(data => {
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
          type: "bar",
          data: {
            labels: data.labels,
            datasets: [{
              label: "Total Bookings",
              data: data.values,
              backgroundColor: data.labels.map(() => "rgba(0,150,199,0.7)"),
              borderColor: "rgba(0,87,179,1)",
              borderWidth: 1,
            }]
          },
          options: {
            responsive: true,
            plugins: { legend: { display: false } },
            onClick: (e, elements) => {
              if (elements.length > 0) {
                const index = elements[0].index;
                const label = data.labels[index];
                loadDetails(label);
              }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        });
      });
  }

  // Load booking details
  function loadDetails(label) {
    fetch(`fetch_report_details.php?label=${label}&view=${currentView}`)
      .then(res => res.json())
      .then(rows => {
        const tableBody = document.querySelector("#details-table tbody");
        tableBody.innerHTML = "";
        if (rows.length > 0) {
          rows.forEach(r => {
            const tr = document.createElement("tr");
            tr.innerHTML = `
              <td>${r.booking_date}</td>
              <td>${r.booking_time}</td>
              <td>${r.petowners_name}</td>
              <td>${r.purpose_of_visit}</td>`;
            tableBody.appendChild(tr);
          });
          document.getElementById("details-section").style.display = "block";
        } else {
          document.getElementById("details-section").style.display = "none";
        }
      });
  }

  // Dropdown listeners
  document.getElementById("dateRange").addEventListener("change", function() {
    currentRange = this.value;
    loadChart();
  });

  document.getElementById("viewType").addEventListener("change", function() {
    currentView = this.value;
    loadChart();
  });

  // Initial load
  loadChart();
  </script>

</body>
</html>
