import request from '@/utils/request';
import axios from 'axios';
export function fetchList(query) {
  return request({ url: '/packages', method: 'get', params: query });
}
export function ConfirmerPelerins(data) {
  return axios
    .get('http://127.0.0.1:8000/api/pelerins/valider/' + data);
}

export function RefuserPelerins(data) {
  return axios
    .get('http://127.0.0.1:8000/api/pelerins/invalider/' + data);
}

