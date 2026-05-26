function uniqueElements(arr) {
    const result = {};
    for (const item of arr) {
        const key = String(item);
        result[key] = (result[key] || 0) + 1;
    }
    return result;
}

console.log(uniqueElements(['привет', 'hello', 1, '1']));
