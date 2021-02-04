import axios from 'axios';

class DataLists {

	constructor () {

		this.datalists = {
              'carriers': [],
              'equipments': [],
              'directions': [],
              'containers': [],
              'harbors': [],
              'currencies': [],
              'surcharges': [],
              'route_types': [],
              'destination_types': [],
              'calculation_types': []
            }

        this.call();
	}

	call () {
		
        /* Return the lists data for dropdowns */
        api.getData({}, '/api/v2/contracts/data', (err, data) => {
            this.setDropdownLists(err, data.data);
        });
	}

	getDataLists(){
		return this.datalists;
	}

    /* Set the Dropdown lists to use in form */
    setDropdownLists(err, data){

        this.datalists = {
          'carriers': data.carriers,
          'equipments': data.equipments,
          'directions': data.directions,
          'containers': data.containers,
          'harbors': data.harbors,
          'currencies': data.currencies,
          'surcharges': data.surcharges,
          'countries': data.countries,
          'route_types': [
                { id: 'port', name: 'Port', vselected: 'harbors' }, 
                { id: 'country', name: 'Country', vselected: 'countries' }
              ],
          'destination_types': data.destination_types,
          'calculation_types': data.calculation_types
        }

    }
}

export default DataLists;
