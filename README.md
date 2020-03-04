	instalation: 
		git clone {link} 
		composer install
		mysql -u {user} -p < create.sql

methods:

exchangeRates: - GET

      - params:
          from - requred
          to - optional (default list of all crypto currencies)    
      call:    
          only "from" parameter:
              /api/exchangeRates/gel/
              /api/exchangeRates/usd/
          "to" too :)
              /api/exchangeRates/gel/btc
              /api/exchangeRates/usd/dash,bch,zec

saveExchangeRates: - POST

      /api/saveExchangeRates - ინახავს ყველა კურსს, მაგრამ გამოტანით getChangeInfo მეთოდში მხოლოდ დოლარის ექვივალენტს გამოიტანს

getChangeInfo: - GET

      note: 
          fromDate and toDate must by in timestamp format 
          you can use strtotime method: php.net/strtotime
      params:
          cryptoCurrency - required
          fromDate - required
          toDate - optional (default now)
      call:
          /api/getChangeInfo/ltc/1583257007/1583336207
          /api/getChangeInfo/btc/1583257007
