export const formatPrice = (value) => {
    return new Intl.NumberFormat('fr-MR', {
        style: 'currency',
        currency: 'MRU' // or 'MRO' if you're using the old code
    }).format(value);
};