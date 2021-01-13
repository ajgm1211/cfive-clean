<template>
    <div>
        
        <Search></Search>

        <Recent></Recent>

        <Result v-if="false"></Result>

    </div>
</template>

<script>
import Search from './Search'; 
import Recent from './Recent';
import Result from './Result'; 

export default {
    components: {
        Search,
        Recent,
        Result
    }
}
</script>