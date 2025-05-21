

export const handlePageChange = (url,routes) => {
    if (!url) return;

    const urlObj = new URL(url);
    const pageParam = urlObj.searchParams.get('page');

    if (pageParam) {
        page.value = parseInt(pageParam);
        router.get(route(routes), {
            search: search.value,
            status: statusFilter.value,
            date: dateFilter.value,
            sort: sort.value.field,
            direction: sort.value.direction,
            page: page.value
        }, {
            preserveState: true,
            preserveScroll: true
        });
    }
};
