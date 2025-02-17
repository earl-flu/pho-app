export function useNumberWithComma() {
    const numberWithComma = (input) => {
        // Convert the input to a number if it's a string
        const number = typeof input === 'string' ? parseFloat(input) : input;

        // Check if the converted input is a valid number
        if (isNaN(number)) {
            return '';
        }

        // Use Intl.NumberFormat to format the number with commas
        return new Intl.NumberFormat().format(number);
    };

    return {
        numberWithComma,
    };
}