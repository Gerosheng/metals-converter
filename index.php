<?php
$convertedAmount = "";
$preciousMetals = [
    'Gold' => 'xau',
    'Silver' => 'xag',
    'Platinum' => 'xpt',
    'Palladium' => 'xpd',
    // Add more metals as needed
];

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    // Get parameters from the URL
    $baseCurrency = isset($_GET['baseCurrency']) ? $_GET['baseCurrency'] : 'usd';

    if (empty($baseCurrency)) {
        // Handle the default case, e.g., set it to 'usd'
        $baseCurrency = 'usd';
    }
    
    $targetCurrency = isset($_GET['targetCurrency']) ? $_GET['targetCurrency'] : 'xau';
    
    if (empty($targetCurrency)) {
        // Handle the default case, e.g., set it to 'xau'
        $targetCurrency = 'xau';
    }

    $apiCurrenciesUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies.json";
    $jsonCurrency = file_get_contents($apiCurrenciesUrl);
    $dataCurrencies = json_decode($jsonCurrency, true);
    
    // Construct the API URL
    $apiUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies/$baseCurrency.json";

    // Fetch the currencies data from the API
    $jsonRates = file_get_contents($apiUrl);

    // Decode JSON response:
    $dataRates = json_decode($jsonRates, true);

    // Check if decoding was successful
    if ($dataRates && isset($dataRates[$baseCurrency][$targetCurrency])) {
        // Get the exchange rate for the specified target currency
        $exchangeRate = $dataRates[$baseCurrency][$targetCurrency];

        // Get the amount from the form
        $amount = isset($_GET['amount']) ? floatval($_GET['amount']) : 1;

        // Calculate the converted amount
        $convertedAmount = $amount * $exchangeRate;

        // Display the result
        echo "Exchange Rate from $baseCurrency to $targetCurrency: $exchangeRate\n";
    } else {
        // Output the API response for debugging
        echo "API Response: " . $jsonRates;
        echo "Error decoding JSON response or invalid base/target currency.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precious Metals Converter</title>
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f4f4;
        color: #333;
        margin: 0;
        padding: 0;
    }

    header {
        background-color: #007bff;
        color: #fff;
        text-align: center;
        padding: 1rem;
    }

    main {
        max-width: 800px;
        margin: 20px auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    form {
        display: flex;
        flex-direction: column;
    }

    label {
        margin-bottom: 8px;
    }

    input, select, button {
        margin-bottom: 16px;
        padding: 8px;
    }

    button {
        background-color: #007bff;
        color: #fff;
        border: none;
        cursor: pointer;
    }

    button:hover {
        background-color: #0056b3;
    }

    #result {
        margin-top: 20px;
    }
</style>
</head>
<body>
    <header>
        <h1>Precious Metals Converter</h1>
    </header>
    <main>
        <form action="" method="get" id="converterForm">
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>

            <label for="baseCurrency">Base Currency:</label>
            <select id="baseCurrency" name="baseCurrency" required>
                <!-- <option value="usd">USD</options> -->
                <?php 
                    foreach ($dataCurrencies as $currencyCode => $currencyName): ?>
                    <?php $selected = ($currencyCode === 'usd') ? 'selected' : ''; ?>
                    <option value="<?php echo $currencyCode; ?>" <?php echo $selected; ?>>
                    (<?php echo strtoupper($currencyCode); ?>) <?php echo $currencyName; ?>
                    </option>
                    <?php endforeach; ?>
            </select>

            <label for="targetCurrency">Target Metal (in ounces):</label>
            <select id="targetCurrency" name="targetCurrency" required>
                <!-- <option value="xau">(XAU) GOLD ounce</option> -->
                <!-- <option value="xpd">(XPD) PALLADIUM ounce</option> -->
                <!-- <option value="xag">(XAG) SILVER ounce</option> -->
                <!-- <option value="xpt">(XPT) PLATINUM ounce</option> -->
                <?php foreach ($preciousMetals as $metal => $symbol): ?>
                    <?php $selected = ($metal === 'Gold') ? 'selected' : ''; ?>
                    <option value="<?php echo $symbol; ?>" <?php echo $selected; ?>>(<?php echo strtoupper($symbol); ?>) <?php echo $metal; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Convert</button>
        </form>

        <div id="result">
            <!-- Display conversion results here -->
            <?php
            if ($convertedAmount) {
                echo "<p>$amount " . strtoupper($baseCurrency) . " =</p>";
                echo "<p>$convertedAmount " . strtoupper($targetCurrency) . "</p>";
                echo "<p>1 " . strtoupper($baseCurrency) . " = " . number_format(1 / $exchangeRate, 7) . " " . strtoupper($targetCurrency) . "</p>";
                echo "<p>1 " . strtoupper($targetCurrency) . " = " . number_format($exchangeRate, 7) . " " . strtoupper($baseCurrency) . "</p>";
            }
            ?>        
    </div>

        <div id="historialData">
            <!-- Display historical data if implemented -->
        </div>

    </main>
</body>
</html>