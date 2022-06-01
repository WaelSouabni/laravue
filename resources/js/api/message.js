import request from '@/utils/request';
import Resource from '@/api/resource';
class MessageResource extends Resource {
  constructor() {
    super('messages');
  }

  permissions(id) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'get',
    });
  }

  updatePermission(id, permissions) {
    return request({
      url: '/' + this.uri + '/' + id + '/permissions',
      method: 'put',
      data: permissions,
    });
  }
  getAllUsers() {
    return request({
      url: '/usersList',
      method: 'get',
    });
  }
}

export { MessageResource as default };
