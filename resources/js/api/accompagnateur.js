import request from '@/utils/request';
import axios from 'axios';

export function ConfirmerAccomp(data) {
  return axios
    .get('http://127.0.0.1:8000/api/accompagnateurs/valider/' + data);
}

export function RefuserAccomp(data) {
  return axios
    .get('http://127.0.0.1:8000/api/accompagnateurs/invalider/' + data);
}
// publier ou masquer
export function publierAccomp(data) {
  return axios
    .get('http://127.0.0.1:8000/api/accompagnateurs/publier/' + data);
}

export function masquerAccomp(data) {
  return axios
    .get('http://127.0.0.1:8000/api/accompagnateurs/masquer/' + data);
}
//
export function fetchPv(id) {
  return request({
    url: '/accompagnateurs/' + id + '/pageviews',
    method: 'get',
  });
}

