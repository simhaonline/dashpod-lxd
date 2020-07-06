# dashpod-lxd
Dashpod-lxd is an LXD / LXC web dashboard that makes it easy to manage the containers on your LXD / LXC servers. Some of the features include:

- Managing remote connections to LXD servers
- Creating, deleting, starting, and stopping of instances (containers)
- Creating and applying snapshots of instances
- Copying/Cloning of instances
- Adding operating system images
- Creating, editing, and deleting of projects, networks, profiles, and storage pools
- Configuring instances using Ansible playbook

This project is an HTML5 web based dashboard used to control the LXD/LXC containers of remote servers. The software runs within a Docker container and is built using Ubuntu, NGINX, PHP, and Ansible.

To get started using the web dashboard first install docker on your computer. Then run:

docker run -d --name dashpod-lxd -p 80:80 -e ADMIN_PASS="<password>" -v ~/dashpod/data:/var/dashpod/data dashpod/dashpod-lxd

For more information visit https://dashpod.org or view the docker information at https://hub.docker.com/r/dashpod/dashpod-lxd