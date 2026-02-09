export const formatCurrency = (amount) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(amount);
};

export const parseCurrency = (value) => {
    return parseFloat(value.toString().replace(/[^0-9.-]+/g, '')) || 0;
};
