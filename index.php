<?php

$convertedAmount = "";

$preciousMetals = [
    'Gold' => 'xau',
    'Silver' => 'xag',
    'Platinum' => 'xpt',
    'Palladium' => 'xpd',
    // Add more metals as needed
];

//default value is 'currencyToMetal'
$exchangeDirection = isset($_GET['exchangeDirection']) ? $_GET['exchangeDirection'] : 'currencyToMetal';

$apiCurrenciesUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies.json";
$jsonCurrency = file_get_contents($apiCurrenciesUrl);
$dataCurrencies = json_decode($jsonCurrency, true);
if ($exchangeDirection === 'currencyToMetal') {
    $baseOptions = $dataCurrencies;
    $targetOptions = $preciousMetals;

} else {
    $baseOptions = $preciousMetals;
    $targetOptions = $dataCurrencies;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['convert'])) {
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

    elseif (isset($_GET['toggleExchangeDirection'])) {
        // Toggle the exchange direction
        $exchangeDirection = ($exchangeDirection === 'currencyToMetal') ? 'metalToCurrency' : 'currencyToMetal';
    

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
            <input type="number" id="amount" name="amount" >

        <?php if ($exchangeDirection === 'metalToCurrency'): ?>

            <label for="targetCurrency">Target Metal (in ounces):</label>
            <select id="targetCurrency" name="targetCurrency" required>
                <?php foreach ($preciousMetals as $metal => $symbol): ?>
                    <?php $selected = ($metal === 'Gold') ? 'selected' : ''; ?>
                    <option value="<?php echo $symbol; ?>" <?php echo $selected; ?>>(<?php echo strtoupper($symbol); ?>) <?php echo $metal; ?></option>
                <?php endforeach; ?>
            </select>

            <label for="baseCurrency">Base Currency:</label>
            <select id="baseCurrency" name="baseCurrency" required>
                <?php 
                    foreach ($dataCurrencies as $currencyCode => $currencyName): ?>
                    <?php $selected = ($currencyCode === 'usd') ? 'selected' : ''; ?>
                    <option value="<?php echo $currencyCode; ?>" <?php echo $selected; ?>>
                    (<?php echo strtoupper($currencyCode); ?>) <?php echo $currencyName; ?>
                    </option>
                    <?php endforeach; ?>
            </select>

        <?php else: ?>

            <label for="baseCurrency">Base Currency:</label>
            <select id="baseCurrency" name="baseCurrency" required>
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
                <?php foreach ($preciousMetals as $metal => $symbol): ?>
                    <?php $selected = ($metal === 'Gold') ? 'selected' : ''; ?>
                    <option value="<?php echo $symbol; ?>" <?php echo $selected; ?>>(<?php echo strtoupper($symbol); ?>) <?php echo $metal; ?></option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>


            <button type="submit" name="convert">Convert</button>

            <button type="submit" name="toggleExchangeDirection">Toggle Direction</button>
            <input type="hidden" name="exchangeDirection" id="exchangeDirection" value="<?php echo $exchangeDirection; ?>">
        
        </form>
        <?php
            echo "Exchange Direction: $exchangeDirection";
        ?>
        <div id="result">
            <!-- Display conversion results here -->
    <?php
        if ($convertedAmount) {
            $baseCurrencySymbol = isset($_GET['baseCurrency']) ? $_GET['baseCurrency'] : 'UnknownBaseCurrency';
            $baseCurrencyName = isset($baseOptions[$baseCurrencySymbol]) ? $baseOptions[$baseCurrencySymbol] : 'Unknown Base Currency';
            $targetCurrencyName = isset($_GET['targetCurrency']) ? ($_GET['targetCurrency']) : 'Unknown Target Currency';

            echo "<p>$amount " . strtoupper($baseCurrencyName) . " =</p>";
            echo "<p>$convertedAmount " . strtoupper($targetCurrencyName) . "</p>";

            if ($exchangeDirection === 'metalToCurrency') {
                echo "<p>1 " . strtoupper($targetCurrencyName) . " = " . number_format(1 / $exchangeRate, 7) . " " . strtoupper($baseCurrencyName) . "</p>";
                echo "<p>1 " . strtoupper($baseCurrencyName) . " = " . number_format($exchangeRate, 7) . " " . strtoupper($targetCurrencyName) . "</p>";
            } else {
                // Corrected the order of units in the following lines
                echo "<p>1 " . strtoupper($baseCurrencyName) . " = " . number_format($exchangeRate, 7) . " " . strtoupper($targetCurrencyName) . "</p>";
                echo "<p>1 " . strtoupper($targetCurrencyName) . " = " . number_format(1 / $exchangeRate, 7) . " " . strtoupper($baseCurrencyName) . "</p>";
            }
        }
    ?>         
    </div>

    </main>
</body>
</html>