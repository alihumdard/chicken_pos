
<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rana Chicken Shop - POS</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7fa; }
        .header { background-color: #007bff; color: white; padding: 10px 20px; display: flex; justify-content: space-between; align-items: center; }
        .header a { color: white; text-decoration: none; padding: 5px 10px; border: 1px solid white; border-radius: 5px; }
        .content { padding: 20px; }
        .section-title { font-size: 18px; margin-bottom: 10px; color: #333; }
        .sub-title { font-size: 14px; color: #666; margin-bottom: 15px; }
        .stats { display: flex; gap: 20px; margin-bottom: 20px; }
        .stat-box { flex: 1; padding: 15px; border-radius: 10px; text-align: center; color: white; text-decoration: none; position: relative; display: flex; align-items: center; justify-content: space-between; }
        .blue { background-color: #007bff; }
        .orange { background-color: #fd7e14; }
        .green { background-color: #28a745; }
        .yellow { background-color: #ffc107; }
        .purple { background-color: #6f42c1; }
        .light-blue { background-color: #e7f3ff; color: #333; }
        .light-orange { background-color: #fff3e0; color: #333; }
        .light-green { background-color: #e6fffa; color: #333; }
        .stat-value { font-size: 24px; margin-bottom: 5px; }
        .stat-label { font-size: 14px; }
        .stat-icon { width: 40px; height: 40px; position: absolute; right: 15px; top: 50%; transform: translateY(-50%); }
        .stat-text { text-align: left; padding-left: 15px; }
        .placeholder { background-color: white; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .buttons { margin-bottom: 20px; text-align: right; }
        .buttons a, .buttons button { text-decoration: none; padding: 8px 15px; margin-left: 10px; border-radius: 5px; color: white; background-color: #007bff; border: none; cursor: pointer; }
        .back { background-color: transparent; color: #007bff; border: 1px solid #007bff; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background-color: white; padding: 20px; border-radius: 10px; width: 400px; box-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .modal-content h2 { margin-top: 0; color: #333; }
        .modal-content label { display: block; margin-bottom: 5px; }
        .modal-content input, .modal-content select { width: 100%; padding: 8px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .modal-content .buttons { margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px; }
        .cancel { background-color: #ccc; color: black; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        table th { background-color: #f2f2f2; }
    </style>

</head>
<body>
    <div class="header">
        <img src="images/chicken.png" alt="Chicken Logo" style="width: 60px; height: 60px; margin-right: 15px;">
        <div style="font-size: 24px; font-weight: bold; margin-left: 20px;">Rana Chicken Shop</div>
        <a href="#">Contact Us</a>
    </div>
    <div class="content">