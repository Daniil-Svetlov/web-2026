const numbers = [2, 5, 8, 10, 3];

const result = numbers
    .map(n => n * 3)
    .filter(n => n > 10);

console.log(result);
