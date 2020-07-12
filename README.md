# dashpod-lxd
Dashpod-lxd is a full-featured LXD / LXC web dashboard that makes it easy to manage the containers on your LXD / LXC servers. Some of the features include:

- Creating/launching new LXD container instances
- Starting, stopping, renaming, and deleting LXD instances
- Cloning/Copying instances
- Locally publishing an image of an instance
- Moving instances between projects and hosts
- Creating, restoring and deleting snapshots of instances
- Downloading LXC images to your host
- Creating, editing, and applying LXD profiles
- Creating and editing networks, storage pools, and projects
- Selecting between projects on a host
- Applying configurations to your containers using Ansible playbooks


This project is an HTML5 web based dashboard used to control the LXD/LXC containers of remote servers. The software runs within a Docker container and is built using Ubuntu, NGINX, PHP, and Ansible.

To get started using the web dashboard first install docker on your computer. Then run:

docker run -d --name dashpod-lxd -p 80:80 -e ADMIN_PASS="dashpod" -v ~/dashpod/data:/var/dashpod/data dashpod/dashpod-lxd

For more information visit https://dashpod.org or view the docker information at https://hub.docker.com/r/dashpod/dashpod-lxd