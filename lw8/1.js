function isPrimeNumber(value) {
    if (typeof value === 'number') {
        if (!Number.isInteger(value) || value < 2) {
            console.log(`${value} не простое число`);
            return;
        }
        let isPrime = true;
        for (let i = 2; i <= Math.sqrt(value); i++) {
            if (value % i === 0) {
                isPrime = false;
                break;
            }
        }
        console.log(isPrime ? `${value} простое число` : `${value} не простое число`);
    } else if (Array.isArray(value)) {
        const primes = [];
        const notPrimes = [];
        for (const item of value) {
            if (typeof item !== 'number' || !Number.isInteger(item) || item < 2) {
                notPrimes.push(item);
                continue;
            }
            let isPrime = true;
            for (let i = 2; i <= Math.sqrt(item); i++) {
                if (item % i === 0) {
                    isPrime = false;
                    break;
                }
            }
            if (isPrime) {
                primes.push(item);
            } else {
                notPrimes.push(item);
            }
        }
        const result = [];
        if (primes.length > 0) result.push(`${primes.join(', ')} простые числа`);
        if (notPrimes.length > 0) result.push(`${notPrimes.join(', ')} не простые числа`);
        console.log(result.join(', '));
    } else {
        console.log('Ошибка: переданный параметр не является числом либо массивом');
    }
}

isPrimeNumber(3);
isPrimeNumber(4);
isPrimeNumber([3, 4, 5]);
isPrimeNumber('hello');
