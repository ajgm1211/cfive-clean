<template>
    <div>
        
        <Search></Search>

        <Recent v-if="false" ></Recent>

        <Result v-if="true"></Result>

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