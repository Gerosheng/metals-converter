<?php

$convertedAmount = "";

$preciousMetals = [
    'Gold' => 'xau',
    'Silver' => 'xag',
    'Platinum' => 'xpt',
    'Palladium' => 'xpd',
];

//default value is 'currencyToMetal'
$exchangeDirection = isset($_GET['exchangeDirection']) ? $_GET['exchangeDirection'] : 'currencyToMetal';

//API for currencies and their symbol
//initialise $dataCurrencies
$apiCurrenciesUrl = "https://cdn.jsdelivr.net/gh/fawazahmed0/currency-api@1/latest/currencies.json";
$jsonCurrency = file_get_contents($apiCurrenciesUrl);
$dataCurrencies = json_decode($jsonCurrency, true);

//determine options values when exchange direction is switched
if ($exchangeDirection === 'currencyToMetal') {
    $baseOptions = $_GET['baseCurrency'];
    $targetOptions = $_GET['targetCurrency'];

} else {
    $baseOptions = $_GET['targetCurrency'];
    $targetOptions = $_GET['baseCurrency'];
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Listens if it's the convert button that has been clicked
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

        // Construct the API URL for latest rates
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
           ($exchangeDirection === 'currencyToMetal') ? $convertedAmount = $amount * $exchangeRate : $convertedAmount = $amount * 1/$exchangeRate;

            // Display the result
            echo "Exchange Rate from $baseCurrency to $targetCurrency: $exchangeRate\n";
        } else {
            // Output the API response for debugging
            echo "API Response: " . $jsonRates;
            echo "Error decoding JSON response or invalid base/target currency.";
        }
    }
    // Listens for exchange directiion toggle
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>

@import url('https://fonts.googleapis.com/css2?family=Nunito:wght@300&family=Poppins&display=swap');

*{
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Nunito', 'Poppins', sans-serif;
}
body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 0 10px;
    background-color: #675AFE;
    margin: 0;
}

::selection{
    color: #fff;
    background: #675AFE;
}

.wrapper{
    width: 80%;
    padding: 30px;
    border-radius: 7px;
    background: #fff;
    box-shadow: 7px 7px 20px rgba(0, 0, 0, 0.05)
}

.wrapper header{
    font-size: 14px;
    font-weight: 500;
    text-align: center;
}

.wrapper form{
    margin: 40px 0 20px 0;
}

form :where(input, select, button){
    width: 100%;
    outline: none;
    border-radius: 5px;
    border: none;
}

form label{
    font-size: 18px;
    margin-bottom: 5px;
}

form input{
    height: 50px;
    font-size: 17px;
    padding: 0 15px;
    border: 1px solid #999;
}
form input:focus{
    padding: 0 14px;
    border: 2px solid #675AFE;
}

form .drop-list{
    display: flex;
    margin-top: 20px;
    align-items: center;
    justify-content: space-between;
}

.drop-list .select-box{
    display: flex;
    width: auto;
    height: 45px;
    align-items: center;
    border-radius: 5px;
    justify-content: center;
    border: 1px solid #999;
}

.select-box img{

}

.select-box select{
    width: auto;
    font-size: 16px;
    background: none;
    margin: 0 -5px 0 5px;
}

.drop-list .swap-icon{
    cursor: pointer;
    margin-top: 30px;
    font-size: 22px;
}

form #convert{
    font-size: 17px;
    margin: 20px 0 30px;
}

form button{
    height: 52px;
    color: #fff;
    font-size: 17px;
    cursor: pointer;
    background: #675AFE;
    transition: 0.3s ease;
}

form button:hover{
    background: #4534fe;
}

label {
    width: 100%;
}

#result {
    margin-top: 20px;
}

/* Responsive styles */
@media only screen and (max-width: 600px) {
    form {
        flex-direction: column;
    }
}
/* Reset flex-direction for larger screens */
@media only screen and (min-width: 601px) {
    form {
        flex-direction: row;
    }
}
 
</style>


</head>
<body>
    <div class="wrapper">
    <header>
        <h1>Precious Metals Converter</h1>
    </header>
    <main>
        <form action="" method="get" id="converterForm">

            <div class="amount">
                <label for="amount">Enter Amount</label>
                <input type="number" id="amount" name="amount" value="<?php echo isset($_GET['amount']) ? htmlspecialchars($_GET['amount']) : 1000; ?>">
            </div>

        <?php if ($exchangeDirection === 'metalToCurrency'): ?>
            <div class="drop-list">
                <div class="from">
            <label for="targetCurrency">Target Metal (in ounces):</label>
            <div class="select-box">
            <select id="targetCurrency" name="targetCurrency" required>
                <?php 
                foreach ($preciousMetals as $metal => $symbol): ?>
                    <?php 
                        $selected = "";
                        if(isset($_GET['targetCurrency']) && $_GET['targetCurrency'] === $symbol){
                            $selected = 'selected';
                        } elseif($metal === 'Gold') {
                            $selected = 'selected';
                        } 
                        ?>
                    <option value="<?php echo $symbol; ?>" <?php echo $selected; ?>>(<?php echo strtoupper($symbol); ?>) <?php echo $metal; ?></option>
                <?php endforeach; ?>
            </select>
            </div>
                </div>
            <div class="swap-icon">
            <button type="submit" name="toggleExchangeDirection"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
            <input type="hidden" name="exchangeDirection" id="exchangeDirection" value="<?php echo $exchangeDirection; ?>">
                </div>
                <div class="to">
            <label for="baseCurrency">Base Currency:</label>
            <div class="select-box">
            <select id="baseCurrency" name="baseCurrency" required>
                <?php foreach ($dataCurrencies as $currencyCode => $currencyName): ?>
                    <option value="<?php echo $currencyCode; ?>" <?php echo ($_GET['baseCurrency'] === $currencyCode || (!isset($_GET['baseCurrency']) && $currencyCode === 'usd')) ? 'selected' : ''; ?>>
                        (<?php echo strtoupper($currencyCode); ?>) <?php echo $currencyName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            </div>
                </div>
            </div>
        <?php else: ?>
            <div class="drop-list">
                <div class="from">
            <label for="baseCurrency">Base Currency:</label>
            <div class="select-box">
            <select id="baseCurrency" name="baseCurrency" required>
                <?php foreach ($dataCurrencies as $currencyCode => $currencyName): ?>
                    <option value="<?php echo $currencyCode; ?>" <?php echo ($_GET['baseCurrency'] === $currencyCode || (!isset($_GET['baseCurrency']) && $currencyCode === 'usd')) ? 'selected' : ''; ?>>
                        (<?php echo strtoupper($currencyCode); ?>) <?php echo $currencyName; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            </div>
                </div>
                <div class="swap-icon">
            <button type="submit" name="toggleExchangeDirection"><i class="fa-solid fa-arrow-right-arrow-left"></i></button>
            <input type="hidden" name="exchangeDirection" id="exchangeDirection" value="<?php echo $exchangeDirection; ?>">
                </div>
                <div class="to">
            <label for="targetCurrency">Target Metal (in ounces):</label>
            <div class="select-box">
            <select id="targetCurrency" name="targetCurrency" required>
                <?php foreach ($preciousMetals as $metal => $symbol): ?>
                    <?php 
                        $selected = "";
                        if(isset($_GET['targetCurrency']) && $_GET['targetCurrency'] === $symbol){
                            $selected = 'selected';
                        } elseif($metal === 'Gold') {
                            $selected = 'selected';
                        } 
                        ?>
                    <option value="<?php echo $symbol; ?>" <?php echo $selected; ?>>(<?php echo strtoupper($symbol); ?>) <?php echo $metal; ?></option>
                <?php endforeach; ?>
            </select>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            
            <button id="convert" type="submit" name="convert">Convert</button>

        
        </form>
        <?php
            echo "Exchange Direction: $exchangeDirection";
        ?>
        <div id="result">
            <!-- Display conversion results here -->
        <?php
            if ($convertedAmount) {
                $baseCurrencySymbol = isset($_GET['baseCurrency']) ? $_GET['baseCurrency'] : 'UnknownBaseCurrency';
                $baseCurrencyName = isset($baseOptions) ? $baseOptions : 'Unknown Base Currency';
                $targetCurrencyName = isset($targetOptions) ? $targetOptions : 'Unknown Target Currency';

                echo "<p id='amount-display'>$amount " . strtoupper($baseCurrencyName) . " =</p>";
                echo "<p id='converted-display'>$convertedAmount " . strtoupper($targetCurrencyName) . "</p>";

                if ($exchangeDirection === 'metalToCurrency') {
                    echo "<p id='fromthis'>1 " . strtoupper($targetCurrencyName) . " = " . number_format($exchangeRate, 7) . " " . strtoupper($baseCurrencyName) . "</p>";
                    echo "<p id='tothat'>1 " . strtoupper($baseCurrencyName) . " = " . number_format(1 / $exchangeRate, 7) . " " . strtoupper($targetCurrencyName) . "</p>";
                } else {
                    // Corrected the order of units in the following lines
                    echo "<p id='fromthis'>1 " . strtoupper($baseCurrencyName) . " = " . number_format($exchangeRate, 7) . " " . strtoupper($targetCurrencyName) . "</p>";
                    echo "<p id='tothat'>1 " . strtoupper($targetCurrencyName) . " = " . number_format(1 / $exchangeRate, 7) . " " . strtoupper($baseCurrencyName) . "</p>";
                }
            }
        ?>         
        </div>

    </main>
    </div>
</body>
</html>