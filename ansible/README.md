# Traefik Server

Hybrid solution:
    Use traditional virtual server but run your apps in Docker containers.
    Great for single server websites etc.

Traefik will work as a proxy to handle traffic and certificate(s).

## Requirements

- Virtual server (e.g. Ubuntu 18 LTS)
- Ansible

## Setup

Download roles:

```
$ ansible-galaxy install -r requirements.yml
```

## Install server

This installs Docker & Traefik and configures them:

```
$ ansible-playbook -u YOURUSER -i YOURHOST/IP, provision.yml
```
