// https://pocketbase.io/docs/js-overview/

console.log("URL PB HOOKS LOADED");

onRecordBeforeCreateRequest((e) => {
    console.log(e.record)
})


