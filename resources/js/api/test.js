import axios from 'axios';
export async function getAll( ) {
    await axios.get('http://localhost:8000/api/usersList').then(response=> {
        return response
    }).catch(error => {
        console.log(error)
    });
}